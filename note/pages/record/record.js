const app = getApp()
const util = require('../../utils/util.js');

const rm = wx.getRecorderManager()
const innerAudioContext = wx.createInnerAudioContext();
Page({

  /**
   * 页面的初始数据
   */
  data: {
    id:'',//笔记id
    title:'',//标题
    content:'',//录音内容
    array: ['默认'],//分类
    index:0,//分类索引
    switch1Checked: false,//是否置顶
    label:'',//标签
    hasRecord:false,//是否存在录音
    duration:'',//录音时长
    fileSize:'',//音频大小
    flag:false,
    word:''//录音识别文字
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(options) {
    // 可以通过 wx.getSetting 先查询一下用户是否授权了 "scope.record" 这个 scope
    wx.openSetting({  
        success: (res) => {
            console.log(res.authSetting);
            if (!res.authSetting['scope.record']) {
                //未设置录音授权
                console.log("未设置录音授权");
            } else {
                //第二次才成功授权
                console.log("设置录音授权成功");
                // recorderManager.start(options);
            }
        },
        fail: function () {
            console.log("授权设置录音失败");
        }
    })

    var that = this
    rm.onStart((res)=>{
        wx.showLoading({
            title: '开始录音...',
        })
    })
    rm.onStop((res)=>{
        console.log(res)
        wx.hideLoading();
        that.setData({
            content:res.tempFilePath,
            hasRecord:true,
            duration:res.duration,
            fileSize:res.fileSize,
            flag:true
        })
    })
    rm.onError((res)=>{
        console.log(res.errMsg)
    })
    app.globalData.session_id = wx.getStorageSync('session_id')
    console.log(app.globalData.session_id)

    var that = this;
    //获取所有分类
    var data = {
      session_id:app.globalData.session_id
    }

    var url = util.jsUrl() + "getClass.php";
    util.funAjax(url, data).then(function(res) {
        var rval = res.data;
        var classList = [];
        for(var i=0;i<rval.length;i++){
          classList.push(rval[i].class_name)
        }
        that.setData({
          array:classList
        })

        if(JSON.stringify(options) !== '{}'){
            that.setData({
                id: options.id
            })
    
            that.getData()
        }
    });

     
  },
  getData(){
    var that = this;

    //获取笔记详情
    var data = {
        id:that.data.id,
        session_id:app.globalData.session_id
    }
  
    var url = util.jsUrl() + "detail.php";
    util.funAjax(url, data).then(function(res) {
        var rval = res.data;
        var index = that.data.array.indexOf(rval.class_name)
        if(index < 0){
          index = 0;
        }
        that.setData({
          title:rval.title,
          label:rval.label,
          content:rval.content,
          index:index,
          hasRecord:true
        })
    });


  },
  bindKeyInput: function(e){
    this.setData({
      title:e.detail.value
    })
  },
  bindLabel(e){
    this.setData({
      label:e.detail.value
    })
  },
  bindPickerChange: function(e) {
    // console.log('picker发送选择改变，携带值为', e.detail.value)
    this.setData({
      index: e.detail.value
    })
  },
  switch1Change(e){
    this.setData({
      switch1Checked:e.detail.value
    })
  },
  upload(){
    //保存笔记
    if(this.data.title.replace(/(^\s*)|(\s*$)/g, "") == ''){
      util.showToast("none", "标题不能为空");
      return;
    }

    if(this.data.content.replace(/(^\s*)|(\s*$)/g, "") == ''){
      util.showToast("none", "内容不能为空");
      return;
    }

    //保存音频
    //执行上传文件操作
    var that = this

    if(that.data.flag){
        wx.uploadFile({
            url: util.jsUrl()+'uploadRecord.php', //仅为示例，非真实的接口地址
            filePath: that.data.content,
            name: 'file',
            header: {
              "Content-Type": "multipart/form-data"
            },
            formData: {
                id:that.data.id,
                session_id:app.globalData.session_id,
                title:that.data.title,
                class_name:that.data.array[that.data.index],
                sort:that.data.switch1Checked,
                content:that.data.content,
                label:that.data.label,
                flag:that.data.flag
            },
            success(res) {
              
                // app.myToast('上传成功！');
                const data = JSON.parse(res.data);//获取到的json 转成数组格式 进行赋值 和渲染图片
                console.log(data);
                var rval = res.data.code;
                if(rval){
                util.showToast("none", "保存失败");
    
                }else{
                    util.showToast("none", "保存成功");
                    setTimeout(function(){
                        wx.redirectTo({
                          url: '/pages/list/list'
                        })
                    },1000)
                }
            },
            fail(e) {
                
            },
            complete(e) {
                
            }
        })
    }else{
        var data = {
            id:that.data.id,
            session_id:app.globalData.session_id,
            title:that.data.title,
            class_name:that.data.array[that.data.index],
            sort:that.data.switch1Checked,
            content:that.data.content,
            label:that.data.label,
            flag:that.data.flag
        }
        var url = util.jsUrl() + "uploadRecord.php";
        util.funAjax(url, data).then(function(res) {
        // console.log(res);
            var rval = res.data.code;
            if(rval){
            util.showToast("none", "保存失败");
    
            }else{
            util.showToast("none", "保存成功");
            setTimeout(function(){
                wx.redirectTo({
                  url: '/pages/list/list'
                })
            },1000)
            }
        });
    }
    
  },
//长按录音
recorderStart(){
    console.log('开始录音')
    var param = {
        format:'mp3'
    }
    rm.start(param)
    console.log(rm)
},
recorderEnd: function () {
    console.log('结束录音')
    //结束录音  
    rm.stop()
},
play(){
  //播放录音
    innerAudioContext.autoplay = true
    innerAudioContext.src = this.data.content,
    innerAudioContext.onPlay(() => {
      console.log('开始播放')
    })
    innerAudioContext.onError((res) => {
      console.log(res.errMsg)
      console.log(res.errCode)
    })
},
trans(){
  //音频转文字
  wx.showLoading({
    title: '正在识别...',
  })
  var that = this
  var data = {
    content:that.data.content,
  }
  var url = util.jsUrl() + "translation.php";
  util.funAjax(url, data).then(function(res) {
  // console.log(res);
      var rval = res.data;
      that.setData({
        word:rval
      })
      wx.hideLoading();
  });
}
})
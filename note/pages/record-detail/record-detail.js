//获取应用实例
const app = getApp()
const util = require('../../utils/util.js')

const innerAudioContext = wx.createInnerAudioContext();
Page({
  data: {
    id:'',//笔记id
    list:'',//笔记详情数组
    title:'',//标题
    content: '', //录音内容
    array: ['默认'],//笔记可选分类
    index:0,//当前分类索引
    switch1Checked: false,//是否置顶
    label:'',//标签
    is_share:false,//是否可分享
    word:''//录音识别文字
  },
  //事件处理函数
  onLoad: function (options) {
    //获取用户唯一id
    app.globalData.session_id = wx.getStorageSync('session_id')
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
        //设置可选分类数组
        that.setData({
          array:classList
        })
    });

    //获取页面参数
    if(JSON.stringify(options) !== '{}'){

        //笔记id参数
        if(options.id != undefined){
          that.setData({
              id:options.id,
          })
        }
        //是否可分享参数
        if(options.is_share != undefined){
          options.is_share == 'false' ? false:true
          that.setData({
              is_share:options.is_share
          })
        }
    }

    //获取笔记详情
    this.getDetail();
  },
  getDetail: function() {
    //获取笔记详情
    var that = this;

    var data = {
        id:that.data.id,
        session_id:app.globalData.session_id,
        is_share:that.data.is_share
    }
    var url = util.jsUrl() + "detail.php";
    util.funAjax(url, data).then(function(res) {
        var rval = res.data;
        that.setData({
            list: rval
        })
        that.setData({
            content:that.data.list.content
        })
        if(rval.session_id == app.globalData.session_id){
          that.setData({
            is_share:false
          })
        }
    });
  },
  edit(){
    //跳转笔记编辑页
    wx.navigateTo({
        url: '/pages/record/record?id='+this.data.id
    })
  },
  onShareAppMessage(res){
    //笔记分享
    let that = this
    if (res.from === 'button') {
      // console.log(res)
    }  
    return {
      title: "笔记分享",
      path: "/pages/record-detail/record-detail?id="+that.data.id+"&is_share=true"
    }
  },
  del(){
    //删除笔记
    var that = this;
    wx.showModal({
      title: '提示',
      content: '确认删除?',
      success (res) {
        if (res.confirm) {
          var data = {
              id:that.data.id,
              session_id:app.globalData.session_id,
          }
          var url = util.jsUrl() + "delNote.php";
          util.funAjax(url, data).then(function(res) {
              var rval = res.data;
              if(rval){
                util.showToast("none", "删除失败");
              }else{
                util.showToast("none", "删除成功");
                setTimeout(function(){
                  wx.redirectTo({
                    url: '/pages/list/list'
                  })
                },1000)
              }
          });
        } else if (res.cancel) {
          // console.log('用户点击取消')
        }
      }
    })
    
  },
  play(){
    //播放笔记录音
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



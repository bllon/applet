const app = getApp()
const util = require('../../utils/util.js');
Page({

  /**
   * 页面的初始数据
   */
  data: {
    id:'',
    title:'',
    articleContent: '', //文章正文
    formats: [],
    readOnly: false,
    placeholder: '开始编辑笔记 . . .',
    editorHeight: 300,
    keyboardHeight: 0,
    isIOS: false,
    bold:'#888',
    array: ['默认'],
    index:0,
    switch1Checked: false,
    label:''
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(options) {
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
          articleContent:rval.content,
          index:index
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
   /** editor 部分 **/
   getEditorValue(e) {
    this.setData({
      articleContent:e.detail.html
    })
  },
  onEditorReady() {
    const that = this
    wx.createSelectorQuery().select('#editor').context(function (res) {
      that.editorCtx = res.context;
      wx.showLoading({
        title: '加载内容中...',
      })
      setTimeout(function(){
        let data = that.data;
        wx.hideLoading();
        that.editorCtx.setContents({
          html: data.pageData ? data.pageData.content:that.data.articleContent,
          success: (res) => {
            // console.log(res)
          },
          fail: (res) => {
            // console.log(res)
          }
        })
      },1000)
    }).exec()
  },
  insertDivider() {
    this.editorCtx.insertDivider({
      success: function () {
        console.log('insert divider success')
      }
    })
  },
  format(e) {
    let { name, value } = e.target.dataset
    if (!name) return
    // console.log('format', name, value)
    this.editorCtx.format(name, value)
    
    var arr = ["bold","italic","strike"];
    if(arr.indexOf(name) >=0 ){
        var index = this.data.formats.indexOf(name)
        if(index < 0){
            this.data.formats.push(name)
        }else{
            this.data.formats.splice(index, 1);
        }
    }

    this.setData({
        formats: this.data.formats
    })
  },
  insertImage() {

    var _this = this;

    wx.chooseImage({
      success(res) {
        // 返回选定照片的本地文件路径列表，tempFilePath可以作为img标签的src属性显示图片
        const tempFilePaths = res.tempFilePaths
        console.log(tempFilePaths)
        wx.showLoading({
          title: '上传中...',
        })
        //执行上传文件操作
        wx.uploadFile({
          url: util.jsUrl()+'uploadImg.php', //仅为示例，非真实的接口地址
          filePath: tempFilePaths[0],
          name: 'file',
          header: {
            "Content-Type": "multipart/form-data"
          },
          formData: {
              'session_id':app.globalData.session_id
          },
          success(res) {
            
            // app.myToast('上传成功！');
            const data = JSON.parse(res.data);//获取到的json 转成数组格式 进行赋值 和渲染图片
            console.log(data);
            _this.editorCtx.insertImage({
              src: data.src,
              data: {
                id: 'abcd',
                role: 'god'
              },
              success: function () {
                console.log('insert image success')
                wx.hideLoading();
                util.showToast("none", "上传成功");
              },
              error: function(){
                console.log('insert image error')
                wx.hideLoading();
                util.showToast("none", "上传失败");
              }
            })
          },
          fail(e) {
            wx.hideLoading();
            console.log(e);
            util.showToast("none", "上传失败");
          },
          complete(e) {
            wx.hideLoading();
            console.log(e);
          }
        })
      }
    })
  },
  upload(){
    //title
    // console.log(this.data.title)
    // console.log(this.data.array[this.data.index])
    //content
    // console.log(this.data.articleContent)

    if(this.data.title.replace(/(^\s*)|(\s*$)/g, "") == ''){
      util.showToast("none", "标题不能为空");
      return;
    }

    if(this.data.articleContent.replace(/(^\s*)|(\s*$)/g, "") == ''){
      util.showToast("none", "内容不能为空");
      return;
    }

    var data = {
      id:this.data.id,
      session_id:app.globalData.session_id,
      title:this.data.title,
      class_name:this.data.array[this.data.index],
      sort:this.data.switch1Checked,
      content:this.data.articleContent,
      label:this.data.label
    }
    var that = this
    var url = util.jsUrl() + "update.php";
    util.funAjax(url, data).then(function(res) {
      // console.log(res);
        var rval = res.data;
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
})
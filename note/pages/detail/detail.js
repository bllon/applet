//获取应用实例
const app = getApp()
const util = require('../../utils/util.js')
Page({
  data: {
    id:'',//笔记id
    list:'',//笔记详情数组
    title:'',//标题
    articleContent: '', //文章正文
    formats: [],//富文本使用格式数组
    placeholder: '开始编辑笔记 . . .',//提示文字
    editorHeight: 300,//高度
    keyboardHeight: 0,
    isIOS: false,
    bold:'#888',//格式颜色
    array: ['默认'],//笔记可选分类
    index:0,//当前分类索引
    switch1Checked: false,//是否置顶
    label:'',//标签
    is_share:false//是否可分享
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
            articleContent:that.data.list.content
        })
        if(rval.session_id == app.globalData.session_id){
          that.setData({
            is_share:false
          })
        }
    });
  },
    /** editor 部分 **/
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
          html: that.data.list.content,
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
  edit(){
    //跳转笔记编辑页
    wx.navigateTo({
        url: '/pages/update/update?id='+this.data.id
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
      path: "/pages/detail/detail?id="+that.data.id+"&is_share=true"
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
    
  }
})



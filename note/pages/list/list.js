//list.js
//获取应用实例
const app = getApp()
const util = require('../../utils/util.js')
Page({
  data: {
    list:'',
    userInfo:'',
    name:'',
    label:'',
    keyword:''
  },
  //事件处理函数
  onLoad: function (options) {
    //获取用户唯一id
    app.globalData.session_id = wx.getStorageSync('session_id')

    //获取页面参数
    if(JSON.stringify(options) !== '{}'){
      //分类参数
      if(options.name != undefined){
        this.setData({
            name:options.name,
        })
      }
      //标签参数
      if(options.label != undefined){
        this.setData({
            label:options.label
        })
      }  
      //获取笔记列表
      this.getData()
    }else{
      //获取笔记列表
      this.getData()
    }

  },
  onPullDownRefresh:function () {
    //下拉刷新
    //获取笔记列表
    this.getData()
    wx.stopPullDownRefresh()
  },
  getData: function() {
    var that = this;

    var data = {
      session_id:app.globalData.session_id,
      name:that.data.name,
      label:that.data.label,
      keyword:that.data.keyword
    }
    var url = util.jsUrl() + "list.php";
    util.funAjax(url, data).then(function(res) {
        var rval = res.data;
        if (rval.length > 0) {
            that.setData({
                list: rval
            })
        }
    });
  },
  add(e){
    wx.navigateTo({
      url: e.currentTarget.dataset.url
    })   
  },
  onNavigateTo(e){
    //跳转详情页
    if(e.currentTarget.dataset.type == '文本'){
      wx.navigateTo({
        url: '/pages/detail/detail?id='+e.currentTarget.id
      })
    }else if(e.currentTarget.dataset.type == '语音'){
      wx.navigateTo({
        url: '/pages/record-detail/record-detail?id='+e.currentTarget.id
      })
    }
    
  },
  search(e){
    //搜索
    console.log('搜索')
    var that = this;
    that.setData({
      keyword: e.detail.value
    })

    var data = {
      session_id:app.globalData.session_id,
      name:that.data.name,
      label:that.data.label,
      keyword:e.detail.value
    }
    var url = util.jsUrl() + "list.php";
    util.funAjax(url, data).then(function(res) {
        var rval = res.data;
        if (rval.length > 0) {
            that.setData({
                list: rval
            })
        }
    });
  }
})



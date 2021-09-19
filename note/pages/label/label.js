//获取应用实例
const app = getApp()
const util = require('../../utils/util.js')
Page({
  data: {
    list:''
  },
  //事件处理函数
  jump: function(e) {
    //跳转对应笔记列表
    wx.navigateTo({
      url: '/pages/list/list?label='+e.currentTarget.dataset.label
    })
  },
  onLoad: function () {
    //获取用户唯一id
    app.globalData.session_id = wx.getStorageSync('session_id')
    //获取笔记标签
    this.getData()
  },
  onPullDownRefresh:function () {
    // 下拉刷新
    //获取笔记标签
    this.getData()
    wx.stopPullDownRefresh()
  },
  getData(){
    //获取笔记标签
    var that = this;

    var data = {
      session_id:app.globalData.session_id
    }
    var url = util.jsUrl() + "getLabel.php";
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
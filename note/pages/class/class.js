//获取应用实例
const app = getApp()
const util = require('../../utils/util.js')
Page({
  data: {
    list:''//分类列表
  },
  //事件处理函数
  jump: function(e) {
    //跳转对应笔记列表
    wx.navigateTo({
      url: '/pages/list/list?name='+e.currentTarget.dataset.name
    })
  },
  onLoad: function () {
    //获取用户唯一id
    app.globalData.session_id = wx.getStorageSync('session_id')
    //获取分类列表
    this.getData()
  },
  onPullDownRefresh:function () {
    // 下拉刷新
    //获取分类列表
    this.getData()
    wx.stopPullDownRefresh()
  },
  getData(){
    //获取分类列表
    var that = this;

    var data = {
      session_id:app.globalData.session_id
    }
    var url = util.jsUrl() + "getClass.php";
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
  del(e){
    //删除对应分类
    var class_name = e.currentTarget.dataset.name
    wx.showModal({
      title: '提示',
      content: '将会删除 '+class_name+' 分类下的所有笔记?',
      success (res) {
        if (res.confirm) {
          var data = {
              class_name:class_name,
              session_id:app.globalData.session_id,
          }
          var url = util.jsUrl() + "delClass.php";
          util.funAjax(url, data).then(function(res) {
              var rval = res.data;
              if(rval){
                util.showToast("none", "删除失败");
              }else{
                util.showToast("none", "删除成功");
                setTimeout(function(){
                  //跳转分类列表
                  wx.redirectTo({
                    url: '/pages/class/class'
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
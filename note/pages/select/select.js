//获取应用实例
const app = getApp()
const util = require('../../utils/util.js')
Page({
  data: {
    list:'',
    nav: [{
        name: '文本',
        link: '../update/update'
      }, {
        name: '语音',
        link: '../record/record'
      }, {
        name: '文件',
        link: '../file/file'
      }]
  },
  //事件处理函数
  jump: function(event) {
    if(event.currentTarget.dataset.link == '../file/file'){
      util.showToast("none", "暂未开启该功能");
    }
    //跳转编辑页
    wx.navigateTo({
      url: event.currentTarget.dataset.link
    })
  },
})
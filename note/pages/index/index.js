//index.js
//获取应用实例
const app = getApp()

Page({
  data: {
    nav: [{
      name: '所有笔记',
      link: '../list/list'
    }, {
      name: '我的分类',
      link: '../class/class'
    }, {
      name: '笔记标签',
      link: '../label/label'
    }, {
      name: '时间地点提醒',
      link: '../logs/logs'
    }, {
      name: '设置',
      link: '../logs/logs'
    }]
  },
  //事件处理函数
  jump: function(event) {
    wx.navigateTo({
      url: event.target.dataset.link
    })
  },
  onLoad: function () {
    
  }
})

// 所有笔记  新增，查看，删除，查找，分享，语音笔记
// 我的分类
// 笔记标签
// 时间地点提醒
// 设置  背景，笔记风格
//index.js
//获取应用实例
const app = getApp()
const util = require('../../utils/util.js')
Page({
  data: {
    motto: '简 易 云 笔 记',
    userInfo: {},
    hasUserInfo: false,
    canIUse: wx.canIUse('button.open-type.getUserInfo')
  },
  //事件处理函数
  bindViewTap: function() {
    //跳转导航页
    wx.navigateTo({
      url: '../index/index'
    })
  },
  onLoad: function () {
    //
    if (app.globalData.userInfo) {
      this.setData({
        userInfo: app.globalData.userInfo,
        hasUserInfo: true
      })
    } else if (this.data.canIUse){
      // 由于 getUserInfo 是网络请求，可能会在 Page.onLoad 之后才返回
      // 所以此处加入 callback 以防止这种情况
      app.userInfoReadyCallback = res => {
        this.setData({
          userInfo: res.userInfo,
          hasUserInfo: true
        })
      }
    } else {
      // 在没有 open-type=getUserInfo 版本的兼容处理
      wx.getUserInfo({
        success: res => {
          app.globalData.userInfo = res.userInfo
          this.setData({
            userInfo: res.userInfo,
            hasUserInfo: true
          })
        }
      })
    }
  },
  getUserInfo: function(e) {
    var that = this;
    if (e != undefined) {
      //判断是否授权成功
      if (e.detail.errMsg == "getUserInfo:fail user deny") {
        util.showToast("none", "授权失败");
        return;
      } else if (e.detail.errMsg == "getUserInfo:fail auth deny") {
        util.showToast("none", "拒绝授权");
        return;
      }
    }

    app.globalData.session_id = wx.getStorageSync('session_id')

    //获取code解析openid 及 sessionKey
    wx.login({
      success: res => {
        // 发送 res.code 到后台换取 openId, sessionKey, unionId
        var userInfo = e.detail.userInfo; //微信账号公共信息

        let params = {
          code:res.code,
          session_id:app.globalData.session_id,
          nickName: userInfo.nickName,
          gender: userInfo.gender,
          city: userInfo.city,
          country:userInfo.country,
          language:userInfo.language,
          province:userInfo.province,
          avatarUrl:userInfo.avatarUrl
        };
        var that = this;
        var url = util.jsUrl() + "login.php";
        util.funAjax(url, params).then(function(v) {
          var rval = v.data;
          console.log(v.data);
          if (v.data.code) {
            //授权失败
            util.showToast("none", "授权失败");
          } else {
            //设置用户本地唯一标识
            app.globalData.session_id = rval.data;
            wx.setStorageSync('session_id', rval.data)
            util.showToast("none", "授权成功");

            app.globalData.userInfo = e.detail.userInfo
            that.setData({
              userInfo: e.detail.userInfo,
              hasUserInfo: true
            })
          }
          
        });
      }
    })

  }
})

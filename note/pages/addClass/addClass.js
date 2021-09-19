//获取APP实例
const app = getApp()
//引入工具文件
const util = require('../../utils/util.js');
Page({

  /**
   * 页面的初始数据
   */
  data: {
    class_name:''//分类名称
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(options) {
    //获取用户唯一id
    app.globalData.session_id = wx.getStorageSync('session_id')
    
  },
  bindKeyInput: function(e){
    //input动态改变分类名称
    this.setData({
      class_name:e.detail.value
    })
  },
  upload(){
    //添加分类，提交给后台
    console.log(this.data.class_name)//分类名称

    //判断分类名称是否为空字符串
    if(this.data.class_name.replace(/(^\s*)|(\s*$)/g, "") == ''){
      util.showToast("none", "分类名称不能为空");
      return;
    }

    var data = {
      session_id:app.globalData.session_id,
      class_name:this.data.class_name,
    }
    var that = this
    var url = util.jsUrl() + "addClass.php";//添加分类后台接口地址
    util.funAjax(url, data).then(function(res) {
        console.log(res);
        var rval = res.data;
        if(rval == 1){
          util.showToast("none", "保存失败");
        }else if(rval == 0){
          util.showToast("none", "保存成功");
          //跳转分类页面
          setTimeout(function(){
            wx.redirectTo({
              url: '/pages/class/class'
            })
          },1000)
        }else{
          util.showToast("none", "已存在该分类");
        }
    });
  }
})
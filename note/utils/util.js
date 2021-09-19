const formatTime = date => {
  const year = date.getFullYear()
  const month = date.getMonth() + 1
  const day = date.getDate()
  const hour = date.getHours()
  const minute = date.getMinutes()
  const second = date.getSeconds()

  return [year, month, day].map(formatNumber).join('/') + ' ' + [hour, minute, second].map(formatNumber).join(':')
}

const formatNumber = n => {
  n = n.toString()
  return n[1] ? n : '0' + n
}
//异步与服务层交互数据
function funAjax(url, data) {
  return new Promise(function (resolve, reject) {
    wx.request({
      url: url,
      data: data,
      method: 'post',
      async: false,
      header: {
        "Content-Type": "application/x-www-form-urlencoded;charset=utf-8",
      },
      success: function (res) {
        resolve(res)
      },
      fail: function (res) {
        reject(res)
      }
    })
  })
}
//后端地址
function jsUrl() {
  var _url = "http://localhost/ybj/";
  return _url;
}
//提示
function showToast(icon, title) {
  wx.showToast({
    title: title,
    icon: icon,
    duration: 1500,
  });
}

module.exports = {
  formatTime: formatTime,
  funAjax: funAjax,
  jsUrl: jsUrl,
  showToast: showToast
}

<template>
  <div id="container">
    <img class="bg" src="http://localhost/static/challenge/bg.jpg" alt="">
    <div>
      <div class="userImg">
        <img :src="userInfo.avatarUrl" alt="">
        <div v-show="statu" style="text-align:center;font-size:40rpx;">{{userInfo.nickName}}</div>
        <button v-show="!statu" open-type="getUserInfo" @getuserinfo="handleGetUserInfo">登录</button>
      </div>
    </div>
    <div style="margin-top:150rpx">
      <i-cell-group>
        <i-cell title="挑战记录" is-link url="/pages/gameHistory/main"></i-cell>
        <i-cell title="扫码对战" is-link @click="scan"></i-cell>
      </i-cell-group>
    </div>  
  </div>
</template>

<script>

export default {
  data(){
    return {
      userInfo:"",
      statu:false
    }
  },
  methods: {
      handleGetUserInfo(ev){
        if(ev.mp.detail.userInfo){
          this.userInfo = ev.mp.detail.userInfo
          this.statu=true
        }
      },
      scan(){
        wx.scanCode({
          success:(res)=>{
            console.log(res)
          }
        })
      }
  },
  onLoad(){
    wx.getUserInfo({
      success:(res)=>{
        console.log(res.userInfo)
        // 更新用户信息
        this.userInfo = res.userInfo
        this.statu=true
      }
    })
  }
}
</script>

<style lang="stylus" rel="stylesheet/stylus">
  #container
    .userImg
      width 100%
      height 400rpx
      background #02a774
      overflow hidden
      img
        display block
        width 150rpx
        height 150rpx
        margin 75rpx auto
        border-radius 20rpx
      button 
        width 200rpx
        height 80rpx
        line-height 80rpx
        text-align center
        margin -20rpx auto
</style>

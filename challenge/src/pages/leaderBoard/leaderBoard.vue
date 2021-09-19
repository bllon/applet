<template>
  <div id="container">
    <img class="bg" src="http://localhost/static/challenge/bg.jpg" alt="">
    <i-tabs :current="current" color="#f759ab" @change="handleChange">
        <i-tab key="tab1" title="单人挑战赛"></i-tab>
        <i-tab key="tab2" title="好友对战"></i-tab>
        <i-tab key="tab3" title="学习浏览"></i-tab>
    </i-tabs>
    <div class="leader" v-show="showLeader">
      <div v-for="(item,index) in singleLeader" :key="index">
        <img v-if="item.userInfo" :src="item.userInfo.avatarUrl" alt="">
        <span class="name" v-if="item.userInfo">{{item.userInfo.nickName}}</span>
        <span class="num">{{item.winNum}}胜</span>
      </div>
    </div>
  </div>
</template>

<script>

export default {
  data(){
    return {
      current:'tab1',
      singleLeader:'',
      showLeader:true
    }
  },
  onLoad(){
    wx.getStorage({
      key: 'sessionId',
      success: function(res) {
        console.log(res)
      },
      fail: function(res) {
        console.log(res)
      }
    })
    wx.request({
      url:'http://localhost/index/challenge/leaderBoard',
      success:(res)=>{
        console.log(res.data)
        this.singleLeader = res.data
      }
    })
  },
  methods:{
    handleChange (ev) {
        console.log(ev)
        this.current=ev.mp.detail.key
        if(this.current == 'tab1'){
          this.showLeader = true
        }else{
          this.showLeader = false
        }
    }
  }
}
</script>

<style lang="stylus" rel="stylesheet/stylus">
#container
  .leader
    width 100%
    height auto
    div
      width 100%
      height 120rpx
      background #ffffff
      border-bottom 1rpx rgba(0,161,241,0.5) solid
      img
        width 80rpx
        height 80rpx
        border-radius 50rpx
        float left
        margin 20rpx 0 20rpx 20rpx
      .name
        height 120rpx
        line-height 120rpx
        float left
        margin-left 20rpx
      .num 
        float right
        height 120rpx
        line-height 120rpx
        margin-right 60rpx

</style>

<template>
  <div id="container">
    <img class="bg" src="http://localhost/static/challenge/bg.jpg" alt="">
    <i-panel title="垃圾分类知识竞赛">
        <!-- <view style="padding: 15px;">PANEL 内容区域</view> -->
    </i-panel>
    <i-notice-bar icon="systemprompt" loop>
        目前单人挑战赛和学习浏览可使用，好友对战存在问题
    </i-notice-bar>
    <i-row>
        <!-- 五个等级的积分 -->
        <i-col span="12" i-class="col-class">
          <button class="btn" @click="singlePlayer">单人挑战赛</button>
        </i-col>
        <i-col span="12" i-class="col-class">
          <button class="btn" @click="doubleBattle">好友对战</button>
        </i-col>
        <i-col span="12" i-class="col-class">
          <button class="btn" @click="learn">学习浏览</button>
        </i-col>
    </i-row>
    <div class="title-img">
      <img src="http://localhost/static/challenge/bg3.gif" alt="">
    </div>
    
  </div>
</template>

<script>
export default {
  data(){
    return {
      current:"index",
    }
  },
  methods:{
    handleChange(ev){
      this.current = ev.mp.detail.key

      if(this.current === 'user'){
          wx.navigateTo({
              url:"/pages/index/main"
          })
      }else if(this.current === 'leaderBoard'){
          wx.navigateTo({
              url:"/pages/leaderBoard/main"
          })
      }
    },
    singlePlayer(){
      //跳转到游戏页
      wx.navigateTo({
        url:"/pages/singlePlayer/main"
      })
    },
    doubleBattle(){
      //跳转到游戏页
      wx.navigateTo({
        url:"/pages/doubleBattle/main"
      })
    },
    learn(){
      //跳转到游戏页
      wx.navigateTo({
        url:"/pages/learn/main"
      })
    }
  }
}
</script>

<style lang="stylus" rel="stylesheet/stylus">
  #container
    .btn
      width 260rpx;
      height 80rpx
      font-size 30rpx
      color #ffffff
      background rgba(0,161,241,0.5)
      text-align center
      line-height 80rpx
      letter-spacing 8rpx
      margin-top 100rpx
    .title-img
      width 100%
      position absolute
      bottom 0
      img 
        display block
        margin 0 auto
</style>

<template>
  <div id="container">
    <img class="bg" src="http://localhost/static/challenge/bg.jpg" alt="">
    <div class="title">学习浏览</div>
    <div class="userbox">
      <div class="user1">
        <img :src="userInfo.avatarUrl" alt="">
        <!-- <div>{{userInfo.nickName}}</div> -->
        <div>{{myScore}}</div>
        <!-- <span>{{sta1}}</span> -->
      </div>      
    </div>
    <div v-show="!statu">
      <span style="display:block;width:50%;margin:0 auto;text-align:center">
        第{{num+1}}题&nbsp;&nbsp;&nbsp;{{time}}s
      </span>     
    </div>
    <button class="btn" :style="nextAllow" v-show="!statu" @click="next">查看答案</button>
    <div class="topic" v-show="!statu">{{currentTimu.title}}<br><span v-show="showAnswer">答案: {{answerName}}</span></div>   
    <div class="option" v-show="!statu" :style="allow">
      <i-panel title="group-水果">
          <i-checkbox-group :current="current" @change="handleFruitChange">
              <i-checkbox i-class="op" :color="color" v-for="(item,index) in currentTimu.options" :key="index" :position="position" :value="item">
              </i-checkbox>
          </i-checkbox-group>
      </i-panel>
    </div>
    <i-toast id="toast" />
  </div>
</template>

<script>
const {$Toast} = require('../../../static/dist/base/index')


export default {
  data(){
    return {
      userInfo:"",
      statu:true,
      myScore:0,//我的分数
      timu:"",//挑战题目
      currentTimu:"",//当前题目
      num:0,//题目序号
      time:15,//答题倒计时
      timer:null,  //计时器
      timuAnswer:"",//题目的答案
      answerName:"",
      myAnswer:-1,//我的答案
      current:[],//当前选择
      position: 'left',
      allow:'',//是否允许点击事件
      nextAllow:'',//查看答案，跳过此题
      color:'',//按钮颜色
      showAnswer:false,//显示答案
      timuNum:""
    }
  },
  onLoad(){

    wx.getUserInfo({

      success:(res)=>{
        // console.log(res.userInfo)
        // 获取用户信息
        this.userInfo = res.userInfo

        //开始匹配队手

        setTimeout(()=>{
          //连接服务器
          wx.connectSocket({
            url: 'ws://localhost:9999',
            header:{
              'content-type': 'application/json'
            }
          })
        },2000) 

      }
    })
    

    //接受消息
    // i 连接服务器成功
    // a 组队成功
    // g 更新分数
    // r 保存游戏记录
    // f 对手离开
    // x 单人学习浏览 
    wx.onSocketOpen((res)=>{
      console.log('WebSocket连接已打开！')

      //发送信息给服务器
      var msg = {'type':'x'}
      wx.sendSocketMessage({
        data: JSON.stringify(msg)
      })

    })

    //接受消息
    wx.onSocketMessage((res)=>{

      var res = JSON.parse(res.data)

      //组队成功消息
      if(res.type == 'x'){
        console.log(res)
        this.statu = false

        this.timu = res.timu
        this.timuNum = res.timu.length
        this.currentTimu = res.timu[this.num]
        this.timuAnswer = res.timu[this.num].answer
        this.answerName = res.timu[this.num].options[this.timuAnswer]

        this.timer = setInterval(()=>{
          this.time--
          if(this.time == 0){
            this.showAnswer=true

            //下一题
            this.num++
            if(this.num < this.timuNum){
              //设置新题目
              this.currentTimu = this.timu[this.num]
              //已选项
              this.current = []
              this.showAnswer=false
              //设置新答案
              this.timuAnswer = this.currentTimu.answer
              this.answerName = this.currentTimu.options[this.timuAnswer]
              //设置可点击选择
              this.allow = 'pointer-events:auto'
              this.nextAllow = 'pointer-events:auto'
              //设置新时间
              this.time = 15
            }else{
              //所有题目答完
              let that = this
              $Toast({
                  content: '已完成随机题目'+that.timuNum+'道',
                  duration : 2
              })
              clearInterval(this.timer)
              this.statu=true
            }
          }
        },1200)
      }
      
    })

    //监听连接是否断开
    wx.onSocketClose((res)=>{
      console.log('服务器已关闭！')
    })

  },
  methods:{
    handleFruitChange(ev) {
      //选择答案
      // console.log(ev.mp.detail.value)
      const index = this.current.indexOf(ev.mp.detail.value);
      index === -1 ? this.current.push(ev.mp.detail.value) : this.current.splice(index, 1);
      this.allow = 'pointer-events:none'
      this.nextAllow = 'pointer-events:none'
      this.showAnswer=true
      this.time = 3

      //判断答案是否正确
      if(ev.mp.detail.value == this.answerName){
        //正确
        this.color = 'green'
        this.myScore += 20
      }else{
        //错误
        this.color = 'red'
      }

    },
    next() {
      this.time = 3
      this.nextAllow = 'pointer-events:none'
      this.allow = 'pointer-events:none'
      this.showAnswer = true
    }
  },
  mounted(){
  },
  destroyed(){
    wx.closeSocket()
    clearInterval(this.timer)
  },
  onUnload: function () {
    // console.log('离开页面')
    //恢复初始值
    this.userInfo = ""
    this.statu=true
    this.timu=""
    this.num=0
    this.time=15
    this.timuAnswer=""
    this.answerName=""
    this.myAnswer=-1
    this.current=[]
    this.position= 'left'
    this.allow=''
    this.myScore=0
    this.showAnswer=false
    wx.closeSocket()
    clearInterval(this.timer)
  },

}
</script>

<style lang="stylus" rel="stylesheet/stylus">
#container
  .title
    width 100%
    height 80rpx
    line-height 80rpx
    text-align center
    color #02a774
    font-weight 400
    letter-spacing 3rpx
  .userbox
    width 100%
    height 230rpx
    .user1
      width 150rpx
      height  220rpx
      margin 50rpx auto
      img 
        width 150rpx
        height 150rpx
        border-radius 75rpx
      div
        width 150rpx
        height 50rpx
        line-height 50rpx
        text-align center
        color blue
        font-weight 400
  .topic
    width 600rpx
    height auto
    margin 60rpx auto
    background rgba(0,0,0,0.5)
    padding 15rpx 30rpx
    line-height 60rpx
    text-align center
    border-radius 20rpx
    text-indent 20rpx
    letter-spacing 3rpx
    color rgba(255,255,255,1)
  .option
    width 100%
    height auto
    position absolute
    bottom 100rpx
    margin 0 auto
    background #ffffff
    text-align center
  .btn
    width 180rpx;
    height 40rpx
    font-size 25rpx
    color #ffffff
    background rgba(0,161,241,0.5)
    text-align center
    line-height 40rpx
    margin 20rpx auto
    letter-spacing 3rpx
</style>

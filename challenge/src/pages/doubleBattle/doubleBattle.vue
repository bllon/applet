<template>
  <div id="container">
    <img class="bg" src="http://localhost/static/challenge/bg.jpg" alt="">
    <div class="title">好友对战</div>
    <div class="userbox">
        <div class="user1">
          <img class="img" :src="userInfo.avatarUrl" alt="">
          <!-- <div>{{userInfo.nickName}}</div> -->
          <div>{{myScore}}</div>
          <!-- <span>{{sta1}}</span> -->
        </div>
          
        <div class="user2">
          <button v-show="statu" class="img" style="font-size:25rpx;text-align:center;line-height:150rpx;color:green;background:rgb(255,255,255,0.8);" open-type="share">邀请</button>
          <img class="img" v-if="otherInfo" :src="otherInfo.avatarUrl" alt="">
          <!-- <div>{{otherInfo.nickName}}</div> -->
          <div v-if="otherInfo">{{otherScore}}</div>
          <!-- <span>{{sta2}}</span> -->
        </div>        
    </div>
    <div style="text-align:center;" v-show="!sta2">{{msg}}</div>
    <button class="btn" v-show="carry" @click="carryOn">继续挑战</button>
    <br>
    <br>
    <div v-show="!statu"><span style="display:block;width:50%;margin:0 auto;text-align:center">第{{num+1}}题&nbsp;&nbsp;&nbsp;{{time}}s</span></div>
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
      userInfo:"",//我的信息
      otherInfo:"",//对手信息
      myId:"",//我的id
      otherId:"",//对手的id
      myScore:0,//我的分数
      otherScore:0,//对手的分数
      sta1:false,//我的准备状态
      sta2:false,//对手的准备状态
      statu:true,//状态显示
      timu:"",//挑战题目
      currentTimu:"",//当前题目
      num:0,//题目序号
      time:15,//答题倒计时
      timer:null,  //计时器
      groupId:"",//分组id
      timuAnswer:"",//题目的答案
      answerName:"",
      myAnswer:-1,//我的答案
      current:[],//当前选择
      position: 'left',
      allow:'',//是否允许点击事件
      color:'',//按钮颜色
      showAnswer:false,//显示答案
      msg:'等待好友中...',
      carry:false,
      timuNum:""
    }
  },
  onLoad(options){
    console.log(options)
    console.log(JSON.stringify(options))
    if(JSON.stringify(options) !== '{}'){
      this.groupId = options.groupId
    }
    

    //获取用户本地唯一标识
    let that = this
    wx.getStorage({
      key: 'sessionId',
      success: function(res) {
        that.myId = res.data
      },
      fail: function(res) {
        console.log(res)
      }
    })

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
        },1000) 

      }
    }),
    
    //接受消息
    // i 连接服务器成功
    // a 组队成功
    // g 更新分数
    // r 保存游戏记录
    // f 对手离开
    // x 单人学习浏览 
    // y 好友对战模式

    wx.onSocketOpen((res)=>{
      console.log('WebSocket连接已打开！')

      //发送个人信息给服务器
      var msg = {'type':'y','userInfo':this.userInfo,'myId':this.myId,'groupId':this.groupId}
      console.log(msg);
      wx.sendSocketMessage({
        data: JSON.stringify(msg)
      })

    })

    //接受消息
    wx.onSocketMessage((res)=>{

      var res = JSON.parse(res.data)

      //连接成功消息
      if(res.type == 'i'){
        console.log(res)
        this.sta1 = true

        if(res.myId != ''){
          //设置用户本地唯一标识
          wx.setStorage({
              key: 'sessionId',
              data: res.myId
          })
        }
        this.myId = res.myId
        this.groupId = res.groupId
      }

      //组队成功消息
      if(res.type == 'a'){
        console.log(res)
        this.sta1 = true
        this.sta2 = true
        this.statu = false

        this.groupId = res.groupId

        if(res.users.one.id == this.myId){
          this.userInfo = res.users.one;
          this.otherInfo = res.users.two;
        }
        if(res.users.two.id == this.myId){
          this.userInfo = res.users.two;
          this.otherInfo = res.users.one;
          this.otherId = res.users.one.id
        }

        this.timu = res.timu
        this.timuNum = res.timu.length
        console.log(this.timu)
        this.currentTimu = res.timu[this.num]
        console.log(this.currentTimu)
        this.timuAnswer = res.timu[this.num].answer
        this.answerName = res.timu[this.num].options[this.timuAnswer]

        console.log(this.timuAnswer)
        console.log(this.answerName)
        console.log(this.currentTimu.options)

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
              //设置新时间
              this.time = 15
            }else{
              //所有题目答完
              clearInterval(this.timer)

              this.statu=true
              this.sta2=false

              var result = ''

              if(this.myScore > this.otherScore){
                result = 'a'
                $Toast({
                    content: '恭喜你获胜了',
                    duration : 2
                })
                this.msg = '恭喜你获胜了'
              }else if(this.myScore < this.otherScore){
                result = 'b'
                $Toast({
                    content: '很遗憾，再接再厉',
                    duration : 2
                })
                this.msg = '很遗憾，再接再厉'
              }else{
                result = 'c'
                $Toast({
                    content: '打成平手',
                    duration : 2
                })
                this.msg = '打成平手'
              }

              //本地保存游戏记录
              //获取当前时间
              var timestamp = Date.parse(new Date());
              timestamp = timestamp / 1000;
              //获取当前时间
              var n = timestamp * 1000;
              var date = new Date(n);
              //年
              var Y = date.getFullYear();
              //月
              var M = (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1);
              //日
              var D = date.getDate() < 10 ? '0' + date.getDate() : date.getDate();
              //时
              var h = date.getHours();
              //分
              var m = date.getMinutes();
              //秒
              // var s = date.getSeconds();

              var time = Y+'-'+M+'-'+D+' '+h+':'+m

              //先查询是否有游戏记录
              var that = this
              wx.getStorage({
                key: 'gameHistory',
                success: function(res) {
                  //新增记录
                  var gamedata = res.data
                  gamedata = JSON.parse(gamedata)
                  var history = {'moshi':1,'time':time,'result':result,'myscore':that.myScore,'other':that.otherScore}
                  gamedata.push(history)
                  gamedata = JSON.stringify(gamedata)

                  wx.setStorage({
                      key: 'gameHistory',
                      data: gamedata
                  })

                },
                fail: function(res) {
                  //新建记录

                  var gamedata = []
                  var history = {'moshi':1,'time':time,'result':result,'myscore':that.myScore,'other':that.otherScore}
                  gamedata.push(history)
                  gamedata = JSON.stringify(gamedata)

                  wx.setStorage({
                      key: 'gameHistory',
                      data: gamedata
                  })
                }


              })

              //发送信息给服务器，保存游戏记录
              var msg = {'type':'r','userInfo':that.userInfo,'groupId':that.groupId,'myId':that.myId,'moshi':1,'time':time,'result':result,'myscore':that.myScore,'other':that.otherScore}
              wx.sendSocketMessage({
                data: JSON.stringify(msg)
              })

              this.carry=true

            }
          }
        },1200)
      }

      //对手离开消息
      if(res.type == 'f'){
        $Toast({
            content: '对方已离开',
            duration : 2
        })
        console.log(res);
        //恢复初始值
        this.otherInfo=""
        this.otherId=""
        this.sta2=false
        this.statu=true
        this.timu=""
        this.num=0
        this.time=15
        this.groupId=""
        this.timuAnswer=""
        this.answerName=""
        this.myAnswer=-1
        this.current=[]
        this.position= 'left'
        this.allow=''
        this.myScore=0
        this.otherScore=0
        this.showAnswer=false
        this.carry=false,
        this.msg='请退出再重新邀请',
        this.timuNum=''

        wx.closeSocket()
        clearInterval(this.timer)
      }

      //更新对手分数消息
      if(res.type == 'g'){

        this.otherScore = res.score

        //更新按钮颜色
        if(res.Judge){
          //答案正确
          $Toast({
              content: '对手答对了',
              type: 'success'
          })
        }else{
          //答案错误
          $Toast({
              content: '对手答错了',
              type: 'error'
          })
        }

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
      this.showAnswer=true

      //判断答案是否正确
      if(ev.mp.detail.value == this.answerName){
        //正确
        this.color = 'green'
        this.myScore += 20
        //发送信息给服务器
        var msg = {'type':'g','groupId':this.groupId,'userId':this.myId,'score':this.myScore,'answer':ev.mp.detail.value,'Judge':true}
        wx.sendSocketMessage({
          data: JSON.stringify(msg)
        })
      }else{
        //错误
        this.color = 'red'
        //发送信息给服务器
        var msg = {'type':'g','groupId':this.groupId,'userId':this.myId,'score':this.myScore,'answer':ev.mp.detail.value,'Judge':false}
        wx.sendSocketMessage({
          data: JSON.stringify(msg)
        })
      }

    },
    carryOn(){
      $Toast({
          content: '再来一局'
      })
    }
  },
  mounted(){
  },
  onShareAppMessage(res){
    let that = this
    if (res.from === 'button') {
      console.log(res)
      console.log(that.groupId)
    }  
    return {
      title: "环保挑战赛-好友邀请",
      path: "pages/doubleBattle/main?groupId="+that.groupId
    }
  },
  destroyed(){
    wx.closeSocket()
    clearInterval(this.timer)
  },
  onUnload: function () {
    // console.log('离开页面')
    //恢复初始值
    this.userInfo = ""
    this.otherInfo=""
    this.myId=""
    this.otherId=""
    this.sta1=false
    this.sta2=false
    this.statu=true
    this.timu=""
    this.num=0
    this.time=15
    this.groupId=""
    this.timuAnswer=""
    this.answerName=""
    this.myAnswer=-1
    this.current=[]
    this.position= 'left'
    this.allow=''
    this.myScore=0
    this.otherScore=0
    this.showAnswer=false
    this.carry=false
    this.msg='等待好友中...'
    this.timuNum=''
    wx.closeSocket()
    clearInterval(this.timer)
  },

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
    position absolute
    bottom 80rpx;
    left 50%
    margin-left -140rpx
    letter-spacing 8rpx
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
    height 300rpx
    .user1
      width 150rpx
      height  220rpx
      float left
      margin 50rpx 0rpx 0rpx 100rpx
      .img 
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
    .user2
      width 150rpx
      height 220rpx
      float right
      margin 50rpx 100rpx 0rpx 0rpx
      .img 
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
    margin 0 auto 0
    background #ffffff
    text-align center
</style>

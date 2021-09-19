<template>
  <div id="container">
    <img class="bg" src="http://localhost/static/challenge/bg.jpg" alt="">
    <div style="width:100%;height:100rpx;line-height:100rpx;text-align:center;">挑战记录</div>
    <div style="margin-top:50rpx">
      <i-cell-group>
        <i-cell v-for="(item,index) in gameHistory" :key="index" :title="item"></i-cell>
      </i-cell-group>
    </div>
  </div>
</template>

<script>

export default {
    data(){
        return {
            gameHistory:[]
        }
    },
    onLoad(){
        var that = this
        wx.getStorage({
            key: 'gameHistory',
            success: function(res) {
                // console.log(JSON.parse(res.data))
                var data = JSON.parse(res.data)
                for(var i=0;i<data.length;i++){
                    var result = ''
                    if(data[i].result == 'a'){
                        result = '胜'
                    }else if(data[i].result == 'b'){
                        result = '输'
                    }else{
                        result = '平'
                    }

                    var moshi = ''
                    if(data[i].moshi == 1){
                        moshi = '单人挑战赛'
                    }else if(data[i].moshi == 2){
                        moshi = '好友对战'
                    }else{
                        moshi = '学习浏览'
                    }
                    var row = data[i].myscore+'分       '+result+'      '+moshi+'      '+data[i].time

                    that.gameHistory.push(row)
                }
            },
            fail: function(res) {
                console.log(res)
            }
        })
    }
}
</script>

<style lang="stylus" rel="stylesheet/stylus">
</style>

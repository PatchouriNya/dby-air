<template>
  <el-row>
    <el-col :span="4">
      <ClientTree ref="tree" style="width: 100%"/>
    </el-col>
    <el-col :span="20">
      <div class="right-content"><img :width="1367" src="@/assets/dtu.jpg" alt=""></div>
    </el-col>
  </el-row>

</template>
<script setup>
import ClientTree from '@/components/ClientTree.vue'
import {onMounted, ref} from 'vue'
import eventBus from '@/listen/event-bus.js'

eventBus.off('defaultNode')
eventBus.off('node-clicked')
const tree = ref()
const client_id = ref()

eventBus.on('node-clicked', (val) => {
  client_id.value = val.id

  console.log(val)
})


onMounted(async () => {
  eventBus.on('defaultNode', (val) => {
    eventBus.emit('node-clicked', val)
  })
})
</script>

<style scoped lang="scss">
.right-content {
  /* 右侧内容的样式 */
  margin: 20px /* 右侧内容的外边距 */

}
</style>
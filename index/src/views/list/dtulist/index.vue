<template>
  <el-row>
    <el-col :span="4">
      <ClientTree ref="tree"/>
    </el-col>
    <el-col :span="20">
      <div class="right-content">123</div>
    </el-col>
  </el-row>

</template>
<script setup>
import ClientTree from "@/components/ClientTree.vue"
import eventBus from "@/listen/event-bus"
import {onMounted, ref} from "vue";

const tree = ref()
const selectData = ref({})
eventBus.on('node-clicked', (val) => {
  selectData.value = val
})
onMounted(() => {
  const defaultCurrentKey = 3
  const defaultExpandedKeys = [3]

  tree.value.defaultExpand(defaultCurrentKey, defaultExpandedKeys)
})
</script>

<style scoped lang="scss">
.right-content {
  /* 右侧内容的样式 */
  height: 100vh; /* 设置高度为整个视口的高度，可根据需要调整 */
  margin: 20px /* 右侧内容的外边距 */
}
</style>
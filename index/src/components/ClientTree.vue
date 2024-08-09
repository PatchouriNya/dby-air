<template>
  <div style="width: 250px">
    <el-skeleton animated :loading="loading">
      <el-tree
          :data="treeData"
          :props="defaultProps"
          node-key="id"
          :expand-on-click-node="true"
          :current-node-key="currentKey"
          :default-expanded-keys="expandedKeys"
          highlight-current
          ref="tree"
          style="background-color: #f5f5f5"
          @node-click="clickNode"
          default-expand-all
      />
    </el-skeleton>

  </div>
</template>

<script setup>
import {ref, onMounted} from 'vue'
import {clientList} from '@/api/client'
import eventBus from '@/listen/event-bus'
import {useClientStore} from '@/store/client.js'

const loading = ref(true)
// 树dom
const tree = ref()
// 树数据
const treeData = ref([])
// 被选中的节点的数据
const selectData = ref({})
// 树结构
const defaultProps = {
  children: 'childs',
  label: 'clientname'
}
const defaultChoose = ref()
// 默认被选中的节点
const currentKey = ref()

// 默认展开的节点数组
const expandedKeys = ref([])

const clientStore = useClientStore()

// 默认展开函数
function defaultExpand(defaultCurrentKey, defaultExpandedKeys) {
  currentKey.value = defaultCurrentKey
  expandedKeys.value = defaultExpandedKeys
}

const findFirstTypeOneNode = (nodes) => {
  for (const node of nodes) {
    if (node.type === 1) {
      return node
    }

    if (node.childs && node.childs.length > 0) {
      const result = findFirstTypeOneNode(node.childs)
      if (result) {
        return result
      }
    }
  }

  return null
}

async function initclientList() {
  loading.value = true
  let res = await clientList()
  treeData.value = [res.data]
  loading.value = false
  defaultChoose.value = findFirstTypeOneNode(treeData.value)
  currentKey.value = defaultChoose.value.id
  expandedKeys.value.push(defaultChoose.value.id)
  eventBus.emit('defaultNode', defaultChoose.value)
}


function clickNode(val) {
  selectData.value = val
  eventBus.emit('node-clicked', val)
  clientStore.clientId = val.id
}


defineExpose({defaultExpand, currentKey, expandedKeys})
onMounted(() => {
  initclientList()
})

</script>

<style scoped lang="scss">

</style>

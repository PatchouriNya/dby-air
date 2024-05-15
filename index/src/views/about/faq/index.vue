<template>
  <el-row>
    <el-col :span="4">
      <el-tree
          style="max-width: 270px;background-color: #f5f5f5"
          :data="menuTree"
          :props="defaultProps"
          highlight-current
          @node-click="handleNodeClick"
          node-key="id"
          :current-node-key="currentNodeKey"
          default-expand-all
      />
    </el-col>
    <el-col :span="20">
      <div class="right-content">
        <h2 style="margin-bottom: 20px;text-align: center">关于{{ title }}</h2>
        <el-card>
          <el-skeleton class="content" v-for="(item, index) in data" :key="item.id" :loading="loading" animated>
            <div class="content">
              <h3 style="margin-bottom: 10px">{{ parseInt(index) + 1 }}.{{ item.question }}</h3>
              <el-text style="white-space: pre-line;font-size: 16px">
                {{ item.answer }}
              </el-text>
            </div>
          </el-skeleton>
        </el-card>
      </div>
    </el-col>
  </el-row>
</template>
<script setup>
import {menuList} from '@/api/menu.js'
import {ref} from 'vue'
import {getQuestionListApi} from '@/api/question.js'

const loading = ref(true)
const menuTree = ref()
const title = ref('帮助中心')

const data = ref({})
const currentNodeKey = ref(0)

async function initMenusList() {
  let res = await menuList()
  menuTree.value = res.data
}

const handleNodeClick = async (val) => {
  loading.value = true
  title.value = val.name
  const res = await getQuestionListApi(val.id)
  data.value = res.data
  setTimeout(() => {
    loading.value = false
  }, 300)

}
const defaultProps = {
  children: 'children',
  label: 'name'
}
initMenusList()
handleNodeClick({id: 31, name: '帮助中心'})
</script>

<style scoped lang="scss">
.right-content {
  margin-left: 20px;
}

.content {
  margin-bottom: 20px;
}
</style>
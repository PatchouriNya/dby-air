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
          :default-expanded-keys="[1]"
      />
    </el-col>
    <el-col :span="20">
      <div class="right-content">
        <h2 style="margin-bottom: 20px;text-align: center">关于{{ title }}</h2>
        <el-card>
          <div v-for="(item, index) in data" :key="item.id">
            <h3>{{ parseInt(index) + 1 }}.{{ item.question }}</h3>
            <br>
            <p> {{ item.answer }}</p>
            <br>
          </div>
        </el-card>
      </div>
    </el-col>
  </el-row>
</template>
<script setup>
import {menuList} from '@/api/menu.js'
import {ref} from 'vue'
import {getQuestionListApi} from '@/api/question.js'

const menuTree = ref()
const title = ref('帮助中心')

const data = ref({
  0: {question: '我该如何使用帮助中心？', answer: '请选择左侧你想要咨询的菜单分类，即可查看相关问题的解答。'},
  1: {question: '我该如何联系客服？', answer: '请使用左侧菜单中关于我们下的联系我们按钮查看联系方式。'}
})
const currentNodeKey = ref(null)

async function initMenusList() {
  let res = await menuList()
  menuTree.value = res.data
}

const handleNodeClick = async (val) => {
  title.value = val.name
  const res = await getQuestionListApi(val.id)
  data.value = res.data
}
const defaultProps = {
  children: 'children',
  label: 'name'
}
initMenusList()
</script>

<style scoped lang="scss">
.right-content {
  margin-left: 20px;
}
</style>
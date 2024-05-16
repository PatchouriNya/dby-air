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
        <h2 style="margin-bottom: 20px;text-align: center">关于 — {{ title }}</h2>
        <div style="margin-bottom: 10px" v-if="isSystem">
          <el-button type="primary" @click="addQuestion">
            新增
          </el-button>
          <el-button type="danger" @click="enterOrExitEditMode">
            {{ editMode ? '不保存' : '编辑模式' }}
          </el-button>
          <h3 style="margin-left: 10px;display: inline-block">请逐个修改！！！</h3>
          <!--          <el-button type="primary" @click="saveQuestion">
                      保存
                    </el-button>-->
        </div>
        <el-card>
          <el-skeleton class="content" v-for="(item, index) in data" :key="item.id" :loading="loading" animated
                       :rows="length">
            <div class="content">
              <div style="display:flex;align-items: center;margin-bottom: 10px">
                <h3 style="margin-top: 3px">{{ parseInt(index) + 1 }}.</h3>
                <h3 class="editable" :contenteditable="editMode"
                    @click="getQuestion(item)"
                    @input="updateQuestion(item, $event)">
                  {{
                    item.question
                  }}</h3> <span v-if="isSystem" style="margin-left: 5px">排序值：</span><span v-if="isSystem"
                                                                                             :contenteditable="editMode"
                                                                                             @click="getQuestion(item)"
                                                                                             @input="updateSort(item, $event)"
                                                                                             style="margin-top: 3px;margin-right: 10px">{{
                  item.sort
                }}</span>
                <el-button v-if="isSystem" type="primary" text @click="saveQuestion">保存</el-button>
                <el-button v-if="isSystem" type="danger" :icon="Delete" text @click="deleteQuestion(item.id)"/>
              </div>


              <el-text ref="answer" class="editable" :contenteditable="editMode"
                       style="white-space: pre-line;font-size: 16px"
                       @click="getQuestion(item)"
                       @input="updateAnswer(item, $event)">
                {{ item.answer }}
              </el-text>
            </div>
          </el-skeleton>
        </el-card>
      </div>
    </el-col>
  </el-row>
  <!--新增-->
  <el-dialog v-model="addVisible" title="新增问题" width="500" :close-on-click-modal="false">
    <el-form :model="addForm" :label-position='"top"'>
      <el-form-item label="问题">
        <el-input v-model="addForm.question" autocomplete="off"/>
      </el-form-item>
      <el-form-item label="回答">
        <el-input v-model="addForm.answer" autocomplete="off"/>
      </el-form-item>
      <el-form-item label="排序值">
        <el-input v-model="addForm.sort" autocomplete="off"/>
      </el-form-item>
    </el-form>
    <template #footer>
      <div class="dialog-footer">
        <el-button @click="addVisible = false">取消</el-button>
        <el-button type="primary" @click="sureAdd">
          确认
        </el-button>
      </div>
    </template>
  </el-dialog>
</template>
<script setup>
import {menuList} from '@/api/menu.js'
import {ref, watch, watchEffect} from 'vue'
import {addQuestionApi, deleteQuestionApi, editQuestionApi, getQuestionListApi} from '@/api/question.js'
import {ElLoading, ElMessage, ElMessageBox} from 'element-plus'
import {Delete} from '@element-plus/icons-vue'
import {getAirTrueDataApi} from '@/api/air.js'
import useAuthControl from '@/hooks/useAuthControl.js'

const {isSystem} = useAuthControl()
const answer = ref()
const qid = ref()
let question = {}
const editMode = ref(false)
const length = ref(0)
const loading = ref(true)
const menuTree = ref()
const title = ref('帮助中心')

const data = ref({})
// 用于不保存恢复数据
const old_data = ref({})
const currentNodeKey = ref(0)

async function initMenusList() {
  let res = await menuList()
  menuTree.value = res.data
}

const handleNodeClick = async (val) => {
  addForm.value.menu_id = val.id
  loading.value = true
  title.value = val.name
  const res = await getQuestionListApi(val.id)
  data.value = res.data
  old_data.value = val
  length.value = res.data.length
  setTimeout(() => {
    loading.value = false
  }, 300)
}
const defaultProps = {
  children: 'children',
  label: 'name'
}
// 新增
const addVisible = ref(false)
const addForm = ref({
  menu_id: null,
  question: '',
  answer: '',
  sort: null
})
const addQuestion = () => {
  addForm.value.question = ''
  addForm.value.answer = ''
  addForm.value.sort = null
  addVisible.value = true
}
const sureAdd = async () => {
  const res = await addQuestionApi(addForm.value)
  if (res.code === 201) {
    ElMessage.success(res.msg)
    addVisible.value = false
    await handleNodeClick(old_data.value)
  }
}
// 编辑
const enterOrExitEditMode = () => {
  if (editMode.value === true)
    handleNodeClick(old_data.value)
  editMode.value = !editMode.value
}
const getQuestion = (item) => {
  qid.value = item.id
  question.question = item.question
  question.answer = item.answer
  question.sort = item.sort
}
const updateQuestion = (item, event) => {
  question.question = event.target.innerText
}
const updateAnswer = (item, event) => {
  question.answer = event.target.innerText
}
const updateSort = (item, event) => {
  question.sort = parseInt(event.target.innerText)
  console.log(question.sort)
}

const saveQuestion = async () => {
  if (qid.value && editMode.value === true) {
    const res = await editQuestionApi(qid.value, question)
    if (res.code === 201) {
      ElMessage.success(res.msg)
      enterOrExitEditMode()
    }
  }
}

// 删除
const deleteQuestion = async (id) => {
  ElMessageBox.confirm(
      '您确定要删除吗?',
      '警告',
      {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }
  )
      .then(async () => {
        const res = await deleteQuestionApi(id)
        if (res.code === 204) {
          ElMessage.success(res.msg)
          await handleNodeClick(old_data.value)
        }
      })
      .catch(() => {

      })


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


/* 设置 focus 状态下的样式 */
.editable:focus {
  outline: 2px solid #409eff; /* 设置边框颜色 */
  border-radius: 4px; /* 可选，设置圆角 */
  padding: 4px; /* 确保 padding 在 focus 时仍然存在 */
}

span:focus {
  outline: 2px solid #409eff; /* 设置边框颜色 */
  border-radius: 4px; /* 可选，设置圆角 */
  padding: 4px; /* 确保 padding 在 focus 时仍然存在 */
}
</style>
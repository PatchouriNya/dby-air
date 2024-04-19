<template>
  <div class="loginPage">
    <div class="title" style="color: black">
      江苏东佰源空调集控系统
    </div>
    <el-card class="loginPanel">
      <div class="loginPanelInner">
        <div class="loginForm">
          <h2 class="loginTitle">用户登录</h2>
          <el-form :model="loginForm" :rules="loginRules" label-width="80px" ref="loginFormRef"
                   @keydown.enter.native="login">
            <el-form-item label="用户名" prop="username">
              <el-input v-model="loginForm.username" placeholder="请输入用户名"></el-input>
            </el-form-item>
            <el-form-item label="密码" prop="password">
              <el-input v-model="loginForm.password" type="password" placeholder="请输入密码"></el-input>
            </el-form-item>
            <el-form-item label="" prop="remember">
              <el-checkbox v-model="loginForm.remember">记住密码</el-checkbox>
            </el-form-item>
            <el-form-item>
              <el-button type="primary" @click="login">立即登录</el-button>
            </el-form-item>
          </el-form>
        </div>
      </div>
    </el-card>
  </div>
</template>

<script setup>
import {getCurrentInstance, ref, onMounted} from 'vue'
import {ElForm, ElFormItem, ElInput, ElButton, ElCheckbox, ElMessage} from 'element-plus'
import {useRouter} from 'vue-router'
import axios from 'axios'
import {useLoginstateStore} from '@/stores/loginstate.js'
import {logCreateApi} from '@/api/log.js'


const loginstateStore = useLoginstateStore()

const loginForm = ref({
  username: '',
  password: '',
  remember: false
})

const loginRules = ref({
  username: [
    {required: true, message: '请输入用户名', trigger: 'blur'}
  ],
  password: [
    {required: true, message: '请输入密码', trigger: 'blur'}
  ]
})

const router = useRouter()
const loginFormRef = ref(null)
const url = getCurrentInstance().proxy.$API_URL + '/login'
const loginLog = ref({
  id: '',
  type: 1,
  content: '登录系统'
})

// 检查本地存储中是否有记住的用户名和密码，并设置初始值
onMounted(() => {
  const rememberName = localStorage.getItem("remember_username");
  const rememberPassword = localStorage.getItem("remember_password");
  if (rememberName && rememberPassword) {
    loginForm.value.username = rememberName;
    loginForm.value.password = rememberPassword;
    loginForm.value.remember = true;
  }
});

async function login() {
  try {
    const response = await axios.post(url, {
      username: loginForm.value.username,
      password: loginForm.value.password
    })
    if (response.data.code === 200) {

      // 如果勾选了记住密码，则将用户名和密码保存到本地存储
      if (loginForm.value.remember) {
        localStorage.setItem("remember_username", loginForm.value.username);
        localStorage.setItem("remember_password", loginForm.value.password);
      } else {
        localStorage.removeItem("remember_username");
        localStorage.removeItem("remember_password");
      }

      localStorage.setItem("token", response.data.data[0].id)
      loginstateStore.id = response.data.data[0].id
      loginLog.value.id = response.data.data[0].id
      await logCreateApi(loginLog.value)
      ElMessage.success('登录成功')
      // 跳转到 index 路由
      await router.push({path: '/'})
      router.go(0)  // 解决了登出后再进来需要手动刷新的问题
    } else {
      ElMessage.error(response.data.msg || '登录失败，请检查用户名和密码')
    }
  } catch (error) {
    ElMessage.error('登录失败，请稍后重试')
  }
}

</script>

<style scoped>
.loginPage {
  width: 100%;
  height: 100vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  //background: linear-gradient(to top, #c9d6ff, #e2e2e2);
  background-image: url("@/assets/login3.jpg");
}

.loginPanel {
  width: 400px;
  padding: 20px;
}

.loginPanelInner {
  display: flex;
  justify-content: center;
}

.loginForm {
  width: 100%;
}

.loginForm h2 {
  font-size: 24px;
  font-weight: normal;
  text-align: center;
  margin-bottom: 20px;
  margin-top: 0;
}

.title {
  font-size: 32px;
  font-weight: bold;
  text-align: center;
  color: #333;
  padding: 20px;
  margin-bottom: 20px;
}

.rememberForget {
  display: flex;
  justify-content: space-between;
  margin-bottom: 10px;
}

.rememberForget .el-link {
  padding-left: 130px;
}

.extraLinks {
  text-align: center;
  margin-top: 10px;
}
</style>

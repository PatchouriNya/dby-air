<template>
  <el-menu :default-active="activeIndex"
           :active-text-color="$menuActiveText"
           :background-color="$menuBg"
           :unique-opened="true"
           class="el-menu-vertical-demo"
           text-color="#fff"
           router
  >
    <router-link to="/main">
      <div class="menu-title">
        <el-icon :size="19" color="skyblue">
          <Cpu/>
        </el-icon>
        <h2 style="font-size: 18px;margin-left: 10px;color: skyblue">空调集控系统</h2>
      </div>
    </router-link>
    <div v-for="level1 in menusList">
      <!--      一级菜单-->
      <el-sub-menu :index="level1.url" :key="level1.id">
        <template #title>
          <el-icon>
            <component :is="level1.icon"/>
          </el-icon>
          {{ level1.name }}
        </template>

        <!--        二级菜单目录-->
        <template v-for="level2 in level1.children" :key="level2.id">
          <el-sub-menu :index="level2.url" v-if="level2.type ===0">
            <template #title>
              <el-icon>
                <component :is="level2.icon"/>
              </el-icon>
              {{ level2.name }}
            </template>
            <!--            三级菜单可点击-->
            <template v-for="level3 in level2.children" :key="level3.id">
              <el-menu-item :index="level3.url">
                <template #title>
                  <el-icon>
                    <component :is="level3.icon"/>
                  </el-icon>
                  {{ level3.name }}
                </template>
              </el-menu-item>
            </template>
          </el-sub-menu>
          <!--        二级菜单可点击-->
          <el-menu-item :index="level2.url" v-else-if="level2.type ===1">
            <template #title>
              <el-icon>
                <component :is="level2.icon"/>
              </el-icon>
              {{ level2.name }}
            </template>
          </el-menu-item>
        </template>
      </el-sub-menu>


    </div>
  </el-menu>
</template>

<script setup>
import {menuList} from "@/api/menu.js"
import {onMounted, ref} from "vue"
import {useRoute} from 'vue-router'
import {Edit} from '@element-plus/icons-vue'

let menusList = ref([])
const activeIndex = ref('')
const iconList = ref(['Odometer', 'Operation', 'Sunset', 'tickets', 'pie-chart'])
const iconList2 = ref(['EditPen', 'EditPen', 'EditPen'])
const $menuActiveText = '#ffffff'
const $menuBg = '#304156'
const route = useRoute()

const icon = ref('menu')


async function initMenusList() {
  let res = await menuList()
  menusList.value = res.data
}

onMounted(() => {
  activeIndex.value = route.path
  initMenusList()
})
</script>

<style lang="scss">
.menu-title {
  color: #fff;
  margin: 20px 0;
  text-align: center;
  border-radius: 5px;
  display: flex;
  align-items: center;
  justify-content: center;
}

</style>

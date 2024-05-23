import {reactive, ref} from 'vue'
import {clientDetailApi, clientEditApi, clientSelectTree, getParentApi} from '@/api/client.js'
import {ElMessage} from 'element-plus'
import {useClientWithoutHeadStore} from '@/store/clientWithoutHead.js'
import {logCreateApi} from '@/api/log.js'


export default function () {
    const clientEditVisible = ref()
    const clientWithoutHeadStore = useClientWithoutHeadStore()
    const accountId = localStorage.getItem('token')
    const id = ref()

    let clientEditForm = reactive({
        clientname: '',
        province: '',
        city: '',
        district: '',
        type: null,
        info: '',
        pid: ''
    })
    // 树数据
    const treeData = ref([])
    const selectValue = ref('')
    // 写日志
    const logForm = reactive({
        id: localStorage.getItem("token"),
        type: 1,
        content: ''
    })

    const showClientEdit = async (row) => {
        id.value = row.id
        clientEditForm.clientname = row.clientname
        clientEditForm.province = row.province
        clientEditForm.city = row.city
        clientEditForm.district = row.district
        clientEditForm.type = row.type
        clientEditForm.info = row.info
        clientEditForm.pid = row.pid
        let parent = await getParentApi(row.id)
        selectValue.value = parent.data.clientname
        let res = await clientSelectTree(row.id)
        treeData.value = [res.data[0]]
        clientEditVisible.value = !clientEditVisible.value
    }

    const cancelClientEdit = () => {
        clientEditVisible.value = !clientEditVisible.value
    }


    const sureClientEdit = async () => {
        const res = await clientEditApi(id.value, clientEditForm)
        if (res.code === 201) {
            logForm.content = '编辑了客户' + clientEditForm.clientname
            await logCreateApi(logForm)
            ElMessage({
                message: res.msg,
                type: 'success'
            })
            await clientWithoutHeadStore.initTable(accountId)
            clientEditVisible.value = !clientEditVisible.value
        }
    }

    return {clientEditVisible, showClientEdit, sureClientEdit, cancelClientEdit, clientEditForm, treeData, selectValue}

}
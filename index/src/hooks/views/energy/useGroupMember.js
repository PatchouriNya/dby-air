import {onMounted, ref} from 'vue'
import {addAirToGroup, getGroupedAirByClientApi, getUnGroupedAirByClientApi, removeAirFromGroup} from '@/api/group.js'
import {ElMessage} from 'element-plus'

export function useGroupMemberList() {
    const listVisible = ref(false)
    const client_id = ref()
    const group_id = ref()
    const memberData = ref([]) // 成员数据
    const memberValue = ref([]) // 已选成员的值

    const generateData = async () => {
        const ungroupedRes = await getUnGroupedAirByClientApi(client_id.value, group_id.value)
        const groupedRes = await getGroupedAirByClientApi(group_id.value)

        if (ungroupedRes && ungroupedRes.data) {
            memberData.value = ungroupedRes.data
                .map(item => ({
                    key: item.id,
                    label: item.designation + '(' + item.show_id + ')', // 使用 designation 作为显示文本
                    disabled: false // 所有未分组的成员都可选
                }))
        }

        if (groupedRes && groupedRes.data) {
            // 添加已分组的成员到 memberValue 中
            memberValue.value = groupedRes.data.map(item => item)
        }
    }

    const showMemberList = async (row) => {
        listVisible.value = true
        client_id.value = row.client_id
        group_id.value = row.id
        await generateData()
    }


    const handleTransferChange = async (newTargetKeys, direction, moveKeys) => {
        if (direction === 'right') {
            // 移动成员进组
            const res = await addAirToGroup(group_id.value, moveKeys.toString())
            if (res.code === 201)
                ElMessage.success(res.msg)
        } else {
            const res = await removeAirFromGroup(group_id.value, moveKeys.toString())
            if (res.code === 204)
                ElMessage.success(res.msg)
        }
    }

    return {listVisible, memberValue, memberData, showMemberList, handleTransferChange}
}
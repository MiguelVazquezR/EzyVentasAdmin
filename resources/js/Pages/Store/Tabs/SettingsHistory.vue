<template>
    <Loading v-if="loading" class="my-12" />
    <div v-else>
        <el-timeline>
            <el-timeline-item v-for="(activity, index) in settingHistories" :key="index" :timestamp="activity.timestamp">
                {{ activity.content }}
            </el-timeline-item>
        </el-timeline>
    </div>
</template>

<script>
import Loading from '@/Components/MyComponents/Loading.vue';
import axios from 'axios';

export default {
    data() {
        return {
            loading: false,
            settingHistories: [],
        }
    },
    components: {
        Loading,
    },
    props: {
        store: Object,
    },
    methods: {
        async fetchSettingsHistories() {
            try {
                this.loading = true;
                const response = await axios.get(route('setting-histories.get-by-store', this.store));

                if (response.status === 200) {
                    this.settingHistories = response.data.items;
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        },
    },
    mounted() {
        this.fetchSettingsHistories();
    }
}
</script>
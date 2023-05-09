<template>
    <div class="container-fluid">
        <breadcrumb :options="['FarmList']">
            <button class="btn btn-primary"  @click="addListDataModal()">Add Farm</button>
        </breadcrumb>
        <div class="row" style="padding:8px 0px;">
        </div>
        <advanced-datatable :options="tableOptions">
            <template slot="action" slot-scope="row">
                <a href="javascript:" @click="addListDataModal(row.item)"> <i class="ti-pencil-alt">Edit</i></a>
            </template>
        </advanced-datatable>
        <add-edit-farmListData @changeStatus="changeStatus" v-if="loading"/>
<!--        <reset-password @changeStatus="changeStatus" v-if="loading"/>-->
    </div>
</template>
<script>
import {Common} from "../../../mixins/common";
import {bus} from "../../../app";
import moment from "moment/moment";

export default {
    mixins: [Common],

    data() {
        return {
            tableOptions: {
                source: 'admin/farmList',
                search: true,
                slots: [7],
                hideColumn: ['CreatedAt'],
                slotsName: ['action'],
                sortable: [2],
                pages: [20, 50, 100],
                addHeader: ['Action'],
                filters: [
                    {
                        type: 'rangepicker',
                    }
                ]
            },
            loading: false,
            cpLoading: false,
        }
    },
    methods: {
        changeStatus() {
            this.loading = false
        },
        addListDataModal(row = '') {
            this.loading = true;
            setTimeout(() => {
                bus.$emit('add-edit-farmlistData', row);
            })
        },
        changePassword(row) {
            this.loading = true;
            setTimeout(() => {
                bus.$emit('edit-password', row);
            })
        },
        deleteDept(id) {
            this.deleteAlert(() => {
                this.axiosDelete('users', id, (response) => {
                    this.successNoti(response.message);
                    this.$store.commit('departmentDelete', id);
                    bus.$emit('refresh-datatable');
                }, (error) => {
                    this.errorNoti(error);
                })
            });
        },
        exportData() {
            bus.$emit('export-data','user-list-'+moment().format('YYYY-MM-DD'))
        }
    }
}
</script>

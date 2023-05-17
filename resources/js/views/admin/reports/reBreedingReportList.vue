<template>
    <div class="container-fluid">
        <breadcrumb :options="['Re Breeding List Report']">
        </breadcrumb>
        <div class="row" style="padding:8px 0px;">
            <div class="col-md-4">
                <button type="button" class="btn btn-success btn-sm" @click="exportData">Export to Excel</button>
            </div>
        </div>
        <advanced-datatable :options="tableOptions" :FarmName="FarmName">
        </advanced-datatable>
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
            FarmName:[],
            tableOptions: {
                source: 'admin/report/reBreedingReportList',
                search: true,
                slots: [10],
                hideColumn: ['CreatedAt'],
                sortable: [2],
                pages: [20, 50, 100],
                showFilter: ['CowCode'],
                colSize: ['col-lg-1','col-lg-1','col-lg-1','col-lg-1','col-lg-2','col-lg-2','col-lg-2','col-lg-2'],
                filters: [
                    {
                        type: 'rangepicker',
                    }
                ]
            },
        }
    },
    methods: {
        changeStatus() {
            this.loading = false
        },
        exportData() {
            bus.$emit('export-data','Re-Breeding-List-'+moment().format('YYYY-MM-DD'))
        }
    }
}
</script>

<template>
    <h3>Report 2</h3>
<!--    {{ Object.keys(errors).length }}-->
    <div class="alert alert-danger" v-if="Object.keys(errors).length > 0">
        <ul>
            <li v-for="error in errors">
                {{ error[0] }}
            </li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label for="head-type">Head Type</label>
                <select class="form-control" id="head-type" v-model="headType">
                    <option value="">Select Head Type</option>
                    <option v-for="(index, type) in headTypes" :value="index">{{ type }}</option>
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="start-date">Start Date</label>
                <input type="date" class="form-control" id="start-date" v-model="startDate" />
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="end-date">End Date</label>
                <input type="date" class="form-control" id="end-date" v-model="endDate" />
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <button class="btn btn-primary" style="margin-top: 2rem" @click="getReport">Get Report</button>
            </div>
        </div>
        <div class="col-12" v-if="!isLoading">
            <div class="table-responsive" v-if="Object.keys(reports).length > 0">
                <table class="table table-bordered">
                    <thead class="bg-light">
                        <tr>
                            <th>#</th>
                            <th>Account Head</th>
                            <th>Opening Balance</th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Closing Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(report, index) in reports">
                            <td>{{ index+1 }}</td>
                            <td>{{ report.name }}</td>
                            <td>{{ (report.opening_balance )}}</td>
                            <td>{{ report.total_debit }}</td>
                            <td>{{ report.total_credit }}</td>
                            <td>{{ report.closing_balance }}</td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <td colspan="2">Total</td>
                            <td>{{ total.opening_balance }}</td>
                            <td>{{ total.debit }}</td>
                            <td>{{ total.credit }}</td>
                            <td>{{ total.closing_balance }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div v-else class="alert alert-danger text-center"> <b>No Data found</b> - Please select options above and click on "Get Report"</div>
        </div>
        <div v-else class="col-12 alert alert-info text-center"> Loading... </div>
    </div>

</template>

<script setup>
import {onMounted, ref} from "vue";
    import moment       from "moment";

    const headTypes = {
        'Asset'    : 'asset',
        'Liability': 'liability',
        'Equity'   : 'equity',
        'Income'   : 'income',
        'Expense'  : 'expense',
    }
    const headType = ref(headTypes.Asset);
    const startDate = ref(moment().subtract(1, 'month').format('YYYY-MM-DD'));
    const endDate = ref(moment().format('YYYY-MM-DD'));
    const errors = ref({});
    const reports = ref({});
    const total = ref({});
    const isLoading = ref(true);
    const getReport = () => {
        errors.value = {}
        reports.value = {}
        total.value = {}
        isLoading.value = true;
        const config = {
            contentType: 'application/json',
            accepts: 'application/json',
        };
        let request = {
            head_type: headType.value,
            start_date: startDate.value,
            end_date: endDate.value,
        };
        axios.post('/report-2', request, config)
            .then(response => {
                reports.value = response.data.report;
                total.value = response.data.total;
                isLoading.value = false;
            })
            .catch(error => {
                errors.value = error.response.data.errors;
                isLoading.value = false;
            });
    }
    onMounted(() => {
        getReport();
    });
</script>

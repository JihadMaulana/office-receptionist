<template>
    <div class="container">
        <template v-if="step === 1">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <p class="text-center" style="color: rgb(217, 236, 255); font-size: 30px;">Hi, I'm DIST the digital receptionist</p>
                    <p class="text-center" style="color: rgb(217, 236, 255); font-size: 30px;">Welcome to The JALA Office</p>
                    <div class="text-center" style="margin: 100px 0 ;">
                        <img class="img-responsive" style="max-width: 250px; margin-left: auto; margin-right: auto" src="https://app.jala.tech/images/logo.svg" alt="">
                    </div>
                    <div class="d-grid gap-2">
                        <a @click.prevent="step++" class="btn btn-default btn-block" style="background-color: rgb(217, 236, 255)">Get Started</a>
                    </div>
                </div>
            </div>
        </template>
        <template v-if="step === 2">
            <div class="row">
                <div class="col-xs-12">
                    <a @click.prevent="step--" style="margin-right: 10px"><font-awesome-icon icon="fa fa-circle-chevron-left" style="color: rgb(217, 236, 255); font-size: 30px;"/></a>
                    <a @click.prevent="step = 1"><font-awesome-icon icon="fa fa-house" style="color: rgb(217, 236, 255); font-size: 30px;"/></a>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <p class="text-center" style="color: rgb(217, 236, 255); font-size: 30px;">Input your name</p>
                    <div class="form-group" style="margin: 100px 0 ;">
                        <input class="form-control" v-model="guestName" placeholder="Type your name here" @keypress.enter="step++"></input>
                    </div>

                    <div class="text-center">
                        <a @click.prevent="step++" class="btn btn-default btn-block" style="background-color: rgb(217, 236, 255)">Submit</a>
                    </div>
                </div>
            </div>
        </template>
        <template v-if="step === 3">
            <div class="row">
                <div class="col-xs-12">
                    <a @click.prevent="step--" style="margin-right: 10px"><font-awesome-icon icon="fa fa-circle-chevron-left" style="color: rgb(217, 236, 255); font-size: 30px;"/></a>
                    <a @click.prevent="step = 1"><font-awesome-icon icon="fa fa-house" style="color: rgb(217, 236, 255); font-size: 30px;"/></a>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <p class="text-center" style="color: rgb(217, 236, 255); font-size: 30px;">Good Morning {{guestName}}</p>
                    <p class="text-center" style="color: rgb(217, 236, 255); font-size: 30px;">Do you already know the person you want to meet?</p>
                    <div class="text-center">
                        <a @click.prevent="step++; knowPerson = true" class="btn btn-default btn-block" style="background-color: rgb(217, 236, 255)">Yes</a>
                        <a @click.prevent="step = 6; knowPerson = false" class="btn btn-default btn-block" style="background-color: rgb(217, 236, 255)">No</a>
                    </div>
                </div>
            </div>
        </template>
        <template v-if="step === 4">
            <div class="row">
                <div class="col-xs-12">
                    <a @click.prevent="step--" style="margin-right: 10px"><font-awesome-icon icon="fa fa-circle-chevron-left" style="color: rgb(217, 236, 255); font-size: 30px;"/></a>
                    <a @click.prevent="step = 1"><font-awesome-icon icon="fa fa-house" style="color: rgb(217, 236, 255); font-size: 30px;"/></a>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <p class="text-center" style="color: rgb(217, 236, 255); font-size: 30px;">Who you want to meet</p>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="form-group" style="margin: 100px 0 ;">
                        <input class="form-control" v-model="searchEmployeeKeyword" placeholder="Type the name here" @keyup="debouncedSearchEmployee"></input>
                        <span v-if="searchEmployeeKeyword && !foundEmployee.length">Sorry the person you found is not found</span>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="text-center" style="margin: 100px 0 ;">
                        <a @click.prevent="searchEmployee" class="btn btn-default btn-block" style="background-color: rgb(217, 236, 255)">Submit</a>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center" v-if="foundEmployee.length > 0">
                <div class="col-md-10">
                    <div style="width: 100%">
                        <el-table
                            :data="foundEmployee"
                            :show-header="false"
                            class="table table-hover"
                            :cell-style="{
                                backgroundColor: '#fff'
                            }"
                            style="width: 100%"
                            @row-click="selectEmployee"
                        >
                            <el-table-column
                                prop="name"
                                label="Name"
                            >
                            </el-table-column>
                            <el-table-column
                                prop="office_status"
                                label="Status"
                                style="text-align: right"
                            >
                                <template slot-scope="scope">
                                    <div v-if="scope.row.office_status == 'in_office'" class="text-right">
                                        available
                                    </div>
                                    <div v-else class="text-right">
                                        unavailable
                                    </div>
                                </template>
                            </el-table-column>
                            <el-table-column
                                prop="division_id"
                                label="Division"
                                style="text-align: right"
                            >
                                <template slot-scope="scope">
                                    <span>{{scope.row.division && scope.row.division.name || '-'}}</span>
                                </template>
                            </el-table-column>
                        </el-table>
                    </div>
                </div>
            </div>
        </template>
        <template v-if="step === 5">
            <div class="row">
                <div class="col-xs-12">
                    <a @click.prevent="step--" style="margin-right: 10px"><font-awesome-icon icon="fa fa-circle-chevron-left" style="color: rgb(217, 236, 255); font-size: 30px;"/></a>
                    <a @click.prevent="step = 1"><font-awesome-icon icon="fa fa-house" style="color: rgb(217, 236, 255); font-size: 30px;"/></a>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <p class="text-center" style="color: rgb(217, 236, 255); font-size: 30px;">Please define your purpose</p>
                    <div class="form-group" style="margin: 100px 0 ;">
                        <textarea class="form-control" v-model="visitPurpose" style="resize: none; height: 80px" placeholder="Define your purposes here"></textarea>
                    </div>

                    <div class="text-center">
                        <a @click.prevent="requestMeet" class="btn btn-default btn-block" style="background-color: rgb(217, 236, 255)">Submit</a>
                    </div>
                </div>
            </div>
        </template>
        <template v-if="step === 6">
            <div class="row">
                <div class="col-xs-12">
                    <a @click.prevent="step--" style="margin-right: 10px"><font-awesome-icon icon="fa fa-circle-chevron-left" style="color: rgb(217, 236, 255); font-size: 30px;"/></a>
                    <a @click.prevent="step = 1"><font-awesome-icon icon="fa fa-house" style="color: rgb(217, 236, 255); font-size: 30px;"/></a>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <p class="text-center" style="color: rgb(217, 236, 255); font-size: 30px;">Your present has been informed to {{knowPerson ? (selectedEmployee && selectedEmployee.name || '-') : 'Receptionist'}}</p>
                    <p class="text-center" style=" margin-bottom: 100px; color: rgb(217, 236, 255); font-size: 30px;">He/She will approach you</p>
                    <p class="text-center" style="color: rgb(217, 236, 255); font-size: 30px;">Thank you for using DIST the digital receptionist</p>
                    <p class="text-center" style="color: rgb(217, 236, 255); font-size: 30px;">Have a nice day</p>
                </div>
            </div>
        </template>
    </div>
</template>

<script>
import _ from "lodash";
import toastr from "toastr";

export default {
    name: "GuestContainer",
    data(){
        return{
            step: 1,
            guestName: null,
            selectedEmployee: null,
            searchEmployeeKeyword: null,
            visitPurpose: null,
            knowPerson: false,
            foundEmployee: [],
        }
    },
    watch:{
      step(newVal, oldVal){
          if (!_.isEqual(newVal, oldVal)){
              if (newVal === 6){
                  setTimeout(()=>{
                      this.resetForm();
                  }, 5000);
              }
          }
      }
    },
    methods:{
        resetForm(){
            this.step = 1
            this.guestName =  null
            this.selectedEmployee =  null
            this.searchEmployeeKeyword =  null
            this.visitPurpose =  null
            this.knowPerson =  false
            this.foundEmployee =  []
        },
        selectEmployee(event){
            if (event.office_status == 'in_office'){
                this.selectedEmployee = event;
                this.step++;
            }else{
                toastr.options.progressbar = true;
                toastr.warning('Employee Unavailable');
            }
        },
        debouncedSearchEmployee: _.debounce(function(){
            return this.searchEmployee();
        }, 200),
        searchEmployee(){
            return axios.get('/api/search_employee',{
                params:{
                    search: this.searchEmployeeKeyword,
                    with: 'division',
                }
            }).then(response => {
                if (response.data.data){
                    this.foundEmployee = response.data.data;
                }
                console.log(response);
                return response
            })
        },
        requestMeet(){
            return axios.post('/api/request_meet',{
                guest_name: this.guestName,
                requested_user_id: this.selectedEmployee && this.selectedEmployee.id || null,
                guest_need: this.visitPurpose
            }).then(response => {
                this.step++;
                console.log(response);
                return response
            }).catch((error) => {
                console.log(error)
            })
        }
    },
    mounted() {
        console.log('Component mounted. changed')
    }
}
</script>

<style scoped>

</style>

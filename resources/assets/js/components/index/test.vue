<template>
    <div  id="test">
<!--        <p>{{title}}</p>-->
<!--        <h1>{{count}}</h1>-->
<!--        <ul>-->
<!--            <li v-for="i in id">{{i.ip}}</li>-->
<!--        </ul>-->
    </div>
</template>
<script>
    export default {
        name:'test',
        props:{
            id:{
            },
            count:{
            }
        },
        data(){
            return {
                title:'test',
                // id:this.id
            }
        }
    }

</script>
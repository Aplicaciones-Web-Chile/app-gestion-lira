<template>
  <div id="q-app">
    <router-view />
  </div>
</template>
<script>
export default {
  name: 'App'
  ,mounted(){
		this.$q.notify.setDefaults({
  			color:'primary'
  			,position:'bottom'
  			,textColor: 'white'
		})
	  
  }
}
</script>

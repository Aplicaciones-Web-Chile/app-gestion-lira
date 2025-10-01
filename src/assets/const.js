export default {
	//backend 		: 'https://appediatra.aplicacionesweb.dev/backend-dashboard-lira/'
	backend: 'https://appgestion.distribuidoralira.cl/backend/'
	, ErrorHandler: vue => {
		vue.$q.notify({
			message: 'Error al intentar conectar con el servidor'
			, color: 'negative'
			, icon: 'error'
		})
	}
}
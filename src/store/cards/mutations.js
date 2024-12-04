export const setId=(state,newValue)=>{
	state.id=newValue;
}
export const setDate=(state,newValue)=>{
	state.date=newValue;
}
export const setCardDetails = (state, payload) => {
    state.selectedCard = payload;
};
// console.log(123);
// // review.successUrl = 'test'
// // Review
// // console.log(review.successUrl);
// // jQuery()
// Review.prototype.save = function () {
//     alert(123)
// }
// var Review = Class.create();
// Review.prototype = {
//     initialize: function (saveUrl, successUrl, agreementsForm) {
//         this.saveUrl = saveUrl;
//         this.successUrl = successUrl;
//         this.agreementsForm = agreementsForm;
//         this.onSave = this.nextStep.bindAsEventListener(this);
//         this.onComplete = this.resetLoadWaiting.bindAsEventListener(this);
//     },

//     save: function () {
//         if (checkout.loadWaiting != false) return;
//         checkout.setLoadWaiting('review');
//         var params = Form.serialize(payment.form);
//         if (this.agreementsForm) {
//             params += '&' + Form.serialize(this.agreementsForm);
//         }
//         params.save = true;
//         var request = new Ajax.Request(
//             this.saveUrl, {
//                 method: 'post',
//                 parameters: params,
//                 onComplete: this.onComplete,
//                 onSuccess: this.onSave,
//                 onFailure: checkout.ajaxFailure.bind(checkout)
//             }
//         );
//     },
// }
// console.log(Review)
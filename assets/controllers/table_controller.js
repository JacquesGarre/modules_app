import { Controller } from '@hotwired/stimulus';
import axios from 'axios';

export default class extends Controller {

    static targets = ["table"]

    static values = {
        url: String,
        page: String
    }

    reload({ detail: { content } }) {
        let table = $('table[data-table="'+content+'"]');
        if(null !== table && table.length){
            let url = table.data('url')
            axios({
                method: 'GET',
                url: url
            })
            .then(function (response) {
                table.html(response.data)
            });
        }

    }

    paginate(){
        let page = this.pageValue;
        let btn = $(this.element);
        let tableSelector = 'table#table-'+btn.data('table');
        let tableFooter = btn.closest('.table-footer');
        let limit = tableFooter.find('.page-limit select').val()
        let table = btn.closest(tableSelector);
        let url = btn.data('url') + '&limit='+limit;

        if(null !== table && table.length){
            axios({
                method: 'GET',
                url: url
            })
            .then(function (response) {
                table.html(response.data)
            });
        }       
    }

    changelimit(){
        let select = $(this.element);
        let tableSelector = 'table#table-'+select.data('table');
        let table = select.closest(tableSelector);
        let newVal = select.val();
        let url = select.data('url') + '?limit='+newVal;

        console.log(table)

        if(null !== table && table.length){
            axios({
                method: 'GET',
                url: url
            })
            .then(function (response) {
                table.html(response.data)
            });
        }     
     
    }

}

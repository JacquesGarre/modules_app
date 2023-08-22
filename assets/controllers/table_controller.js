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
        let url = btn.data('url');
        let tableID = 'table#table-'+btn.data('table');
        let table = btn.closest('.nav-pagination').siblings('table');

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

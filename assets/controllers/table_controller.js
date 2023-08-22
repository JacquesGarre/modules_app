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

    applyFilters(){

        let element = $(this.element);
        let tableSelector = 'div#table-'+element.data('table');
        let table = element.closest(tableSelector);
        let tableFooter = table.find('.table-footer');
        let url = element.data('url');

        // get filters
        let filters = {}
        table.find('.filter-field').each(function(){
            filters[$(this).attr('name')] = $(this).val()
        })

        // get page
        let page = this.pageValue ?? 1;

        // get limit
        let limit = tableFooter.find('.page-limit select').val();
        

        // build url
        url += "?page="+page+"&limit="+limit+"&filters="+JSON.stringify(filters)
       
        // reload table html
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

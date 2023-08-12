import { Controller } from '@hotwired/stimulus';
import axios from 'axios';

export default class extends Controller {

    static targets = []

    static values = {
        url: String
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

}

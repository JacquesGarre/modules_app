import { Controller } from '@hotwired/stimulus';
import axios from 'axios';

export default class extends Controller {

    static targets = []

    static values = {
        url: String
    }

    reload({ detail: { content } }) {
        let layout = $('.page-builder[data-table="'+content+'"]');
        if(null !== layout && layout.length){
            let url = layout.data('url') + '?ajax=1';
            axios({
                method: 'GET',
                url: url
            })
            .then(function (response) {
                layout.html(response.data)
            });
        }
    }

}
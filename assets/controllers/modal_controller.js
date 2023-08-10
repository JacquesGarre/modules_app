import { Controller } from '@hotwired/stimulus';
import { Modal } from 'bootstrap';
import jQuery from "jquery";
window.$ = window.jQuery = jQuery;
import axios from 'axios';

export default class extends Controller {

    static values = {
        url: String
    }

    open() {
        axios
        .get(this.urlValue)
        .then((response) => {
            $('#modal').find('.modal-dialog').html(response.data);
            const modal = Modal.getInstance('#modal');
            modal.show()
        });
    }

    close() {
        const modal = Modal.getInstance('#modal');
        modal.hide()
    }

}

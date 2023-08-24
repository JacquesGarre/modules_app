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
        $('.fixed-plugin').addClass('show')
    }

    close() {
        $('.fixed-plugin').removeClass('show')
    }

}

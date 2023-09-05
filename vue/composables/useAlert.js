import swal from "sweetalert";

export function useAlert(){
    function flash(label, message, type){
        return swal(label, message, type)
    }

    return flash;
}

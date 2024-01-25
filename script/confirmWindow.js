export function createConfirm (idPass = null) {
    const back = document.createElement('div');
    back.id = 'confirmBack';
    const formConfirm = document.createElement('div');
    formConfirm.id = 'confirmForm';
    const p = document.createElement('p');
    p.innerHTML = 'Deseja mesmo realizar essa ação?'
    const buttonDecline = document.createElement('button');
    buttonDecline.innerHTML = 'Não';
    buttonDecline.addEventListener('click', ()=> {
        back.remove();
    })
    const buttonAccept = document.createElement('button');
    buttonAccept.innerHTML = 'Sim';
    buttonAccept.id = idPass;
    buttonAccept.classList.add('acceptButton');
    formConfirm.appendChild(p);
    formConfirm.appendChild(buttonDecline);
    formConfirm.appendChild(buttonAccept);
    back.appendChild(formConfirm);
    document.body.appendChild(back);
}
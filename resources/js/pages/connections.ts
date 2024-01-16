import axios from 'axios';

window.addEventListener('load', ()=> {
    const connections = document.querySelectorAll('div[data-connectiontype]') as NodeListOf<HTMLDivElement>;
    connections.forEach(connection => {
        const { connectiontype: connectionType } = connection.dataset;

        const buttons = connection.querySelectorAll('button[data-type]') as NodeListOf<HTMLButtonElement>;
        buttons.forEach(button => {
            const { type: buttonType } = button.dataset;

            button.addEventListener('click', async e => {
                const { data: url } = await axios.post(`/user/connections/${connectionType}/${buttonType}`);

                window.location.href = url;
            })
        });
    })
})
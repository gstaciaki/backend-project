import './NewTaskButton.css'

const NewTaskButton = ({ onClick }) => {

    return (
        <div className='newTask'>
            <button onClick={onClick}>Adicionar nova tarefa</button>
        </div>
    )
}

export default NewTaskButton
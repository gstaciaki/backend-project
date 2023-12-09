import './NewTaskField.css'

const NewTaskField = ({ onKeyDown }) => {
    return (
        <div className='newTaskField'>
            <input placeholder='Nome da nova tarefa...' onKeyDown={onKeyDown} />
        </div>
    )
}

export default NewTaskField
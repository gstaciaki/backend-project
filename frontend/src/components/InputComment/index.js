import './InputComment.css'
import UploadButton from '../UploadButton'
import SendButton from '../SendButton'

const InputComment = () => {

    return (
        <div className='inputComment'>
            <input placeholder='Faca um comentario...'></input>
            <UploadButton />
            <SendButton />
        </div>
    )
}

export default InputComment
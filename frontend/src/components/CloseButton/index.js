import './CloseButton.css'

const CloseButton = ( {onClick} ) => {

    return (
        <div className='closeBt'>
            <button onClick={onClick}></button>
        </div>
    )
}

export default CloseButton
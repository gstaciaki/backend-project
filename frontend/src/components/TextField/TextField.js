import './TextField.css'

const TextField = (props) => {
    return (
        <input className="textInput" placeholder={props.placeholder} type={props.type}/>
    )
}

export default TextField
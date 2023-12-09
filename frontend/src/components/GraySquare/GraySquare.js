import './GraySquare.css'

const GraySquare = ({ children }) => {
    return (
        <div className="background">
            {children}
        </div>
    )
}

export default GraySquare
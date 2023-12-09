import GraySquare from '../../components/GraySquare/GraySquare';
import Logo from '../../components/Logo/Logo'
import RedButton from '../../components/RedButton/RedButton';
import TextField from '../../components/TextField/TextField';
import './Login.css'

const Login = () => {
    return (
        <div className="App">
            <Logo />
            <GraySquare>
                <TextField placeholder="Endereço de email..." />
                <TextField type="password" placeholder="Senha..." />
                <RedButton text="LOGIN" to="/taskList" />
            </GraySquare>
        </div>
    );
}

export default Login
import './keyboard.css'

const Keyboard = ({ children }) => {
    return (
        <div>
            <div className="outerbox">
                <div className="virkeyout">
                    <div className="virkey">
                        <div className="keypad_back">
                            {children}
                        </div>
                    </div>
                    <div className="thickness">
                    </div>
                </div>
            </div>
        </div>
    );
}

export default Keyboard;


/**
 * Interface that should be implemented by the different
 * program's interface
 */
public interface UI {
  /**
   * Ask the user to enter its keys (certificate and encryption key)
   */
  void askKeys(KeyManager manager);

  /**
   * Launch the user interface
   */
  void run(KeyManager manager);
}

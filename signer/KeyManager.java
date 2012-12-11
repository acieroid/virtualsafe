import java.io.InputStream;

/**
 * Class that manage the keys and operations with those keys (signing,
 * verifying, decrypting)
 */
public class KeyManager {
    /** The directory where the keys are located */
    private String dir;

    /**
     * Create a new key manager, that stores and read keys from the
     * directory @param dir
     */
    public KeyManager(String dir) {
        this.dir = dir;
    }

    /**
     * Check if the keys of the user already exists.
     * @return true if the keys exists, else false.
     */
    public boolean keysExists() {
        return false; /* TODO */
    }

    /**
     * Parse the description of the certificate and encryption keys
     * from @param stream.
     * @return true on success, else false.
     */
    public boolean parse(InputStream stream) {
        return false; /* TODO */
    }

    /**
     * Sign the file @param file and store the resulting signature
     * to @param signature.
     * @return true on success, else false.
     */
    public boolean sign(String file, String signature) {
        return false; /* TODO */
    }

    /**
     * Check that the file @param file match its signature, stored
     * at @param signature.
     * @return true on success, else false.
     */
    public boolean check(String file, String signature) {
        return false; /* TODO */
    }

    /**
     * Decrypt the file @param fileIn with the key stored (encrypted)
     * in @param keyFile, and save the decrypted file to @param
     * fileOut.
     * @return true on success, else false.
     */
    public boolean decrypt(String fileIn, String fileOut, String keyFile) {
        return false; /* TODO */
    }
}

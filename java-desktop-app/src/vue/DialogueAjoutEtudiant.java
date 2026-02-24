package vue;

import javax.swing.*;
import java.awt.*;
import modele.Etudiant;

/**
 * Dialogue pour ajouter un nouvel tudiant
 */
public class DialogueAjoutEtudiant extends JDialog {
    
    private JTextField txtNom;
    private JTextField txtPrenom;
    private JTextField txtEmail;
    private JTextField txtLogin;
    private JPasswordField txtMotDePasse;
    private JComboBox<String> comboGenre;
    private JCheckBox checkRedoublant;
    private JCheckBox checkAnglophone;
    private JComboBox<String> comboTypeBac;
    private JComboBox<String> comboMentionBac;
    
    private boolean confirme = false;
    private boolean modeEdition = false;
    private JButton btnValider;
    
    public DialogueAjoutEtudiant(Frame parent) {
        super(parent, "Ajouter un étudiant", true);
        this.modeEdition = false;
        initComponents();
        setSize(500, 600);
        setLocationRelativeTo(parent);
    }
    
    public DialogueAjoutEtudiant(Frame parent, Etudiant etudiant) {
        super(parent, "Modifier un étudiant", true);
        this.modeEdition = true;
        initComponents();
        remplirChamps(etudiant);
        setSize(500, 600);
        setLocationRelativeTo(parent);
    }
    
    private void remplirChamps(Etudiant etudiant) {
        txtNom.setText(etudiant.getNom());
        txtPrenom.setText(etudiant.getPrenom());
        txtEmail.setText(etudiant.getEmail());
        txtLogin.setText(etudiant.getEmail().split("@")[0]); // Login basé sur l'email
        txtMotDePasse.setText(""); // Ne pas afficher le mot de passe
        
        // Genre
        comboGenre.setSelectedItem(String.valueOf(etudiant.getGenre()));
        
        // Type de bac
        if (etudiant.getTypeBac() != null) {
            comboTypeBac.setSelectedItem(etudiant.getTypeBac());
        }
        
        // Mention bac
        if (etudiant.getMentionBac() != null && !etudiant.getMentionBac().isEmpty()) {
            comboMentionBac.setSelectedItem(etudiant.getMentionBac());
        } else {
            comboMentionBac.setSelectedItem("Sans mention");
        }
        
        checkRedoublant.setSelected(etudiant.isEstRedoublant());
        checkAnglophone.setSelected(etudiant.isEstAnglophone());
    }
    
    private void initComponents() {
        setLayout(new BorderLayout(10, 10));
        
        // Panel principal avec formulaire
        JPanel panelForm = new JPanel(new GridBagLayout());
        panelForm.setBorder(BorderFactory.createEmptyBorder(20, 20, 20, 20));
        GridBagConstraints gbc = new GridBagConstraints();
        gbc.fill = GridBagConstraints.HORIZONTAL;
        gbc.insets = new Insets(5, 5, 5, 5);
        
        int row = 0;
        
        // Nom
        gbc.gridx = 0;
        gbc.gridy = row;
        gbc.weightx = 0.3;
        panelForm.add(new JLabel("Nom *:"), gbc);
        
        gbc.gridx = 1;
        gbc.weightx = 0.7;
        txtNom = new JTextField(20);
        panelForm.add(txtNom, gbc);
        row++;
        
        // Prénom
        gbc.gridx = 0;
        gbc.gridy = row;
        gbc.weightx = 0.3;
        panelForm.add(new JLabel("Prénom *:"), gbc);
        
        gbc.gridx = 1;
        gbc.weightx = 0.7;
        txtPrenom = new JTextField(20);
        panelForm.add(txtPrenom, gbc);
        row++;
        
        // Email
        gbc.gridx = 0;
        gbc.gridy = row;
        gbc.weightx = 0.3;
        panelForm.add(new JLabel("Email *:"), gbc);
        
        gbc.gridx = 1;
        gbc.weightx = 0.7;
        txtEmail = new JTextField(20);
        panelForm.add(txtEmail, gbc);
        row++;
        
        // Login
        gbc.gridx = 0;
        gbc.gridy = row;
        gbc.weightx = 0.3;
        panelForm.add(new JLabel("Login *:"), gbc);
        
        gbc.gridx = 1;
        gbc.weightx = 0.7;
        txtLogin = new JTextField(20);
        panelForm.add(txtLogin, gbc);
        row++;
        
        // Mot de passe
        gbc.gridx = 0;
        gbc.gridy = row;
        gbc.weightx = 0.3;
        JLabel lblMdp = new JLabel(modeEdition ? "Nouveau mot de passe:" : "Mot de passe *:");
        panelForm.add(lblMdp, gbc);
        
        gbc.gridx = 1;
        gbc.weightx = 0.7;
        txtMotDePasse = new JPasswordField(20);
        panelForm.add(txtMotDePasse, gbc);
        
        if (modeEdition) {
            // En mode édition, le mot de passe est optionnel
            gbc.gridx = 1;
            gbc.gridy = row + 1;
            JLabel lblInfo = new JLabel("(Laisser vide pour ne pas changer)");
            lblInfo.setFont(new Font("Arial", Font.ITALIC, 11));
            lblInfo.setForeground(Color.GRAY);
            panelForm.add(lblInfo, gbc);
            row++;
        }
        row++;
        
        // Genre
        gbc.gridx = 0;
        gbc.gridy = row;
        gbc.weightx = 0.3;
        panelForm.add(new JLabel("Genre *:"), gbc);
        
        gbc.gridx = 1;
        gbc.weightx = 0.7;
        comboGenre = new JComboBox<>(new String[]{"H", "F"});
        panelForm.add(comboGenre, gbc);
        row++;
        
        // Type de bac
        gbc.gridx = 0;
        gbc.gridy = row;
        gbc.weightx = 0.3;
        panelForm.add(new JLabel("Type de bac *:"), gbc);
        
        gbc.gridx = 1;
        gbc.weightx = 0.7;
        comboTypeBac = new JComboBox<>(new String[]{
            "Général", "Technologique", "Professionnel", "Autre"
        });
        panelForm.add(comboTypeBac, gbc);
        row++;
        
        // Mention bac
        gbc.gridx = 0;
        gbc.gridy = row;
        gbc.weightx = 0.3;
        panelForm.add(new JLabel("Mention bac:"), gbc);
        
        gbc.gridx = 1;
        gbc.weightx = 0.7;
        comboMentionBac = new JComboBox<>(new String[]{
            "Sans mention", "Assez bien", "Bien", "Très bien"
        });
        panelForm.add(comboMentionBac, gbc);
        row++;
        
        // Redoublant
        gbc.gridx = 0;
        gbc.gridy = row;
        gbc.weightx = 0.3;
        panelForm.add(new JLabel("Redoublant:"), gbc);
        
        gbc.gridx = 1;
        gbc.weightx = 0.7;
        checkRedoublant = new JCheckBox();
        panelForm.add(checkRedoublant, gbc);
        row++;
        
        // Anglophone
        gbc.gridx = 0;
        gbc.gridy = row;
        gbc.weightx = 0.3;
        panelForm.add(new JLabel("Option anglais:"), gbc);
        
        gbc.gridx = 1;
        gbc.weightx = 0.7;
        checkAnglophone = new JCheckBox();
        panelForm.add(checkAnglophone, gbc);
        row++;
        
        add(panelForm, BorderLayout.CENTER);
        
        // Panel boutons
        JPanel panelBoutons = new JPanel(new FlowLayout(FlowLayout.RIGHT));
        panelBoutons.setBorder(BorderFactory.createEmptyBorder(10, 10, 10, 10));
        
        JButton btnAnnuler = new JButton("Annuler");
        btnAnnuler.addActionListener(e -> {
            confirme = false;
            dispose();
        });
        
        btnValider = new JButton(modeEdition ? "Modifier" : "Ajouter");
        btnValider.setBackground(new Color(139, 28, 58)); // Bordeaux
        btnValider.setForeground(Color.WHITE);
        btnValider.setFocusPainted(false);
        btnValider.addActionListener(e -> valider());
        
        panelBoutons.add(btnAnnuler);
        panelBoutons.add(btnValider);
        
        add(panelBoutons, BorderLayout.SOUTH);
    }
    
    private void valider() {
        // Vérifier les champs obligatoires
        if (txtNom.getText().trim().isEmpty() ||
            txtPrenom.getText().trim().isEmpty() ||
            txtEmail.getText().trim().isEmpty() ||
            txtLogin.getText().trim().isEmpty()) {
            
            JOptionPane.showMessageDialog(this,
                "Veuillez remplir tous les champs obligatoires (*)",
                "Champs manquants",
                JOptionPane.WARNING_MESSAGE);
            return;
        }
        
        // En mode ajout, le mot de passe est obligatoire
        if (!modeEdition && txtMotDePasse.getPassword().length == 0) {
            JOptionPane.showMessageDialog(this,
                "Le mot de passe est obligatoire lors de l'ajout d'un étudiant",
                "Mot de passe manquant",
                JOptionPane.WARNING_MESSAGE);
            return;
        }
        
        // Valider l'email
        String email = txtEmail.getText().trim();
        if (!email.matches("^[A-Za-z0-9+_.-]+@(.+)$")) {
            JOptionPane.showMessageDialog(this,
                "Format d'email invalide",
                "Email incorrect",
                JOptionPane.WARNING_MESSAGE);
            return;
        }
        
        confirme = true;
        dispose();
    }
    
    public boolean estConfirme() {
        return confirme;
    }
    
    public String getNom() {
        return txtNom.getText().trim();
    }
    
    public String getPrenom() {
        return txtPrenom.getText().trim();
    }
    
    public String getEmail() {
        return txtEmail.getText().trim();
    }
    
    public String getLogin() {
        return txtLogin.getText().trim();
    }
    
    public String getMotDePasse() {
        return new String(txtMotDePasse.getPassword());
    }
    
    public String getGenre() {
        return (String) comboGenre.getSelectedItem();
    }
    
    public String getTypeBac() {
        return (String) comboTypeBac.getSelectedItem();
    }
    
    public String getMentionBac() {
        String mention = (String) comboMentionBac.getSelectedItem();
        return mention.equals("Sans mention") ? null : mention;
    }
    
    public boolean estRedoublant() {
        return checkRedoublant.isSelected();
    }
    
    public boolean estAnglophone() {
        return checkAnglophone.isSelected();
    }
}

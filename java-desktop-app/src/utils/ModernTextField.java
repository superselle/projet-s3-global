package utils;

import javax.swing.*;
import javax.swing.border.EmptyBorder;
import java.awt.*;
import java.awt.event.FocusAdapter;
import java.awt.event.FocusEvent;

/**
 * JTextField avec style moderne et coins arrondis
 */
public class ModernTextField extends JTextField {
    private int cornerRadius = 8;
    private Color borderColor = new Color(200, 200, 200);
    private Color focusBorderColor = new Color(52, 152, 219);
    private boolean isFocused = false;

    public ModernTextField() {
        super();
        initialize();
    }

    public ModernTextField(int columns) {
        super(columns);
        initialize();
    }

    public ModernTextField(String text) {
        super(text);
        initialize();
    }

    private void initialize() {
        setOpaque(false);
        setBorder(new EmptyBorder(10, 15, 10, 15));
        setFont(new Font("Arial", Font.PLAIN, 13));
        setBackground(Color.WHITE);

        addFocusListener(new FocusAdapter() {
            @Override
            public void focusGained(FocusEvent e) {
                isFocused = true;
                repaint();
            }

            @Override
            public void focusLost(FocusEvent e) {
                isFocused = false;
                repaint();
            }
        });
    }

    @Override
    protected void paintComponent(Graphics g) {
        Graphics2D g2 = (Graphics2D) g.create();
        g2.setRenderingHint(RenderingHints.KEY_ANTIALIASING, RenderingHints.VALUE_ANTIALIAS_ON);

        int width = getWidth();
        int height = getHeight();

        // Fond
        g2.setColor(getBackground());
        g2.fillRoundRect(0, 0, width, height, cornerRadius, cornerRadius);

        // Bordure
        g2.setColor(isFocused ? focusBorderColor : borderColor);
        g2.setStroke(new BasicStroke(isFocused ? 2 : 1));
        g2.drawRoundRect(isFocused ? 1 : 0, isFocused ? 1 : 0, 
                        width - (isFocused ? 2 : 1), height - (isFocused ? 2 : 1), 
                        cornerRadius, cornerRadius);

        g2.dispose();
        super.paintComponent(g);
    }

    @Override
    protected void paintBorder(Graphics g) {
        // Ne rien faire, la bordure est dessinée dans paintComponent
    }
}

namespace win2kai
{
    partial class Form1
    {
        /// <summary>
        /// Required designer variable.
        /// </summary>
        private System.ComponentModel.IContainer components = null;

        /// <summary>
        /// Clean up any resources being used.
        /// </summary>
        /// <param name="disposing">true if managed resources should be disposed; otherwise, false.</param>
        protected override void Dispose(bool disposing)
        {
            if (disposing && (components != null))
            {
                components.Dispose();
            }
            base.Dispose(disposing);
        }

        #region Windows Form Designer generated code

        /// <summary>
        /// Required method for Designer support - do not modify
        /// the contents of this method with the code editor.
        /// </summary>
        private void InitializeComponent()
        {
            this.lblModel = new System.Windows.Forms.Label();
            this.cmbModels = new System.Windows.Forms.ComboBox();
            this.chkRetro = new System.Windows.Forms.CheckBox();
            this.rtbChat = new System.Windows.Forms.RichTextBox();
            this.txtInput = new System.Windows.Forms.TextBox();
            this.btnSend = new System.Windows.Forms.Button();
            this.SuspendLayout();
            // 
            // lblModel
            // 
            this.lblModel.AutoSize = true;
            this.lblModel.Location = new System.Drawing.Point(9, 407);
            this.lblModel.Name = "lblModel";
            this.lblModel.Size = new System.Drawing.Size(84, 13);
            this.lblModel.TabIndex = 0;
            this.lblModel.Text = "Select AI model:";
            // 
            // cmbModels
            // 
            this.cmbModels.DropDownStyle = System.Windows.Forms.ComboBoxStyle.DropDownList;
            this.cmbModels.FormattingEnabled = true;
            this.cmbModels.Location = new System.Drawing.Point(102, 403);
            this.cmbModels.Name = "cmbModels";
            this.cmbModels.Size = new System.Drawing.Size(121, 21);
            this.cmbModels.TabIndex = 1;
            // 
            // chkRetro
            // 
            this.chkRetro.AutoSize = true;
            this.chkRetro.Location = new System.Drawing.Point(238, 403);
            this.chkRetro.Name = "chkRetro";
            this.chkRetro.Size = new System.Drawing.Size(177, 17);
            this.chkRetro.TabIndex = 2;
            this.chkRetro.Text = "Back in time (Year 2000 Modus)";
            this.chkRetro.UseVisualStyleBackColor = true;
            // 
            // rtbChat
            // 
            this.rtbChat.Anchor = ((System.Windows.Forms.AnchorStyles)((((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Bottom)
                        | System.Windows.Forms.AnchorStyles.Left)
                        | System.Windows.Forms.AnchorStyles.Right)));
            this.rtbChat.Location = new System.Drawing.Point(12, 12);
            this.rtbChat.Name = "rtbChat";
            this.rtbChat.ReadOnly = true;
            this.rtbChat.ScrollBars = System.Windows.Forms.RichTextBoxScrollBars.Vertical;
            this.rtbChat.Size = new System.Drawing.Size(543, 384);
            this.rtbChat.TabIndex = 3;
            this.rtbChat.Text = "";
            // 
            // txtInput
            // 
            this.txtInput.Anchor = ((System.Windows.Forms.AnchorStyles)((((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Bottom)
                        | System.Windows.Forms.AnchorStyles.Left)
                        | System.Windows.Forms.AnchorStyles.Right)));
            this.txtInput.Location = new System.Drawing.Point(12, 431);
            this.txtInput.Multiline = true;
            this.txtInput.Name = "txtInput";
            this.txtInput.Size = new System.Drawing.Size(462, 23);
            this.txtInput.TabIndex = 4;
            this.txtInput.TextChanged += new System.EventHandler(this.txtInput_TextChanged);
            // 
            // btnSend
            // 
            this.btnSend.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Right)));
            this.btnSend.Location = new System.Drawing.Point(480, 431);
            this.btnSend.Name = "btnSend";
            this.btnSend.Size = new System.Drawing.Size(75, 23);
            this.btnSend.TabIndex = 5;
            this.btnSend.Text = "Send";
            this.btnSend.UseVisualStyleBackColor = true;
            this.btnSend.Click += new System.EventHandler(this.btnSend_Click);
            // 
            // Form1
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(567, 466);
            this.Controls.Add(this.btnSend);
            this.Controls.Add(this.txtInput);
            this.Controls.Add(this.rtbChat);
            this.Controls.Add(this.chkRetro);
            this.Controls.Add(this.cmbModels);
            this.Controls.Add(this.lblModel);
            this.Name = "Form1";
            this.Text = "Retro chat";
            this.Load += new System.EventHandler(this.Form1_Load);
            this.ResumeLayout(false);
            this.PerformLayout();

        }

        #endregion

        private System.Windows.Forms.Label lblModel;
        private System.Windows.Forms.ComboBox cmbModels;
        private System.Windows.Forms.CheckBox chkRetro;
        private System.Windows.Forms.RichTextBox rtbChat;
        private System.Windows.Forms.TextBox txtInput;
        private System.Windows.Forms.Button btnSend;
    }
}

